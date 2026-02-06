<?php

namespace App\Services;

use App\Models\CommissionRuleModel;
use App\Models\CommissionOverrideModel;
use App\Models\TransactionCommissionModel;
use App\Models\CommissionLogModel;

/**
 * Commission Calculator Service
 * 
 * Core business logic for commission calculation with hierarchical override system:
 * User > Role > Agency > System Default
 * 
 * Supports:
 * - Percentage-based commissions
 * - Fixed amount commissions
 * - Month-based commissions (for rentals)
 * - VAT calculation (TTC from HT)
 * - Commission splits (agent vs agency)
 */
class CommissionCalculatorService
{
    protected $ruleModel;
    protected $overrideModel;
    protected $commissionModel;
    protected $logModel;

    public function __construct()
    {
        $this->ruleModel = new CommissionRuleModel();
        $this->overrideModel = new CommissionOverrideModel();
        $this->commissionModel = new TransactionCommissionModel();
        $this->logModel = new CommissionLogModel();
    }

    /**
     * Calculate commission for a transaction
     * 
     * @param array $transactionData Transaction details
     * @param int $userId User ID for override resolution
     * @param int $roleId Role ID for override resolution
     * @param int $agencyId Agency ID for override resolution
     * @param bool $persist Whether to save to database
     * @return array Calculated commission details
     */
    public function calculateCommission(
        array $transactionData, 
        int $userId, 
        int $roleId, 
        int $agencyId,
        bool $persist = true
    ): array {
        // Extract transaction details
        $transactionType = $transactionData['transaction_type']; // 'sale' or 'rent'
        $propertyType = $transactionData['property_type'];
        $transactionAmount = (float) $transactionData['amount']; // Sale price or monthly rent
        $transactionId = $transactionData['transaction_id'] ?? null;
        $propertyId = $transactionData['property_id'];
        $agentId = $transactionData['agent_id'] ?? $userId;

        // Step 1: Get applicable rule (with override hierarchy)
        $rule = $this->getApplicableRule($userId, $roleId, $agencyId, $transactionType, $propertyType);

        if (!$rule) {
            throw new \Exception("No commission rule found for {$transactionType} - {$propertyType}");
        }

        // Step 2: Calculate buyer/tenant commission
        $buyerCommission = $this->calculateCommissionForParty(
            $transactionAmount,
            $rule['buyer_commission_type'],
            $rule['buyer_commission_value'],
            $rule['buyer_commission_vat']
        );

        // Step 3: Calculate seller/owner commission
        $sellerCommission = $this->calculateCommissionForParty(
            $transactionAmount,
            $rule['seller_commission_type'],
            $rule['seller_commission_value'],
            $rule['seller_commission_vat']
        );

        // Step 4: Calculate totals
        $totalCommissionHT = $buyerCommission['ht'] + $sellerCommission['ht'];
        $totalCommissionVAT = $buyerCommission['vat'] + $sellerCommission['vat'];
        $totalCommissionTTC = $buyerCommission['ttc'] + $sellerCommission['ttc'];

        // Step 5: Calculate agent/agency split
        $agentPercentage = $transactionData['agent_commission_percentage'] ?? 50.00;
        $agentCommissionAmount = $totalCommissionTTC * ($agentPercentage / 100);
        $agencyCommissionAmount = $totalCommissionTTC - $agentCommissionAmount;

        // Step 6: Build result
        $result = [
            'transaction_id' => $transactionId,
            'property_id' => $propertyId,
            'rule_id' => $rule['rule_id'] ?? null,
            'override_id' => $rule['override_id'] ?? null,
            'override_level' => $rule['override_level'],
            'transaction_type' => $transactionType,
            'property_type' => $propertyType,
            'transaction_amount' => $transactionAmount,
            
            // Buyer/Tenant commission
            'buyer_commission_ht' => $buyerCommission['ht'],
            'buyer_commission_vat' => $buyerCommission['vat'],
            'buyer_commission_ttc' => $buyerCommission['ttc'],
            'buyer_commission_type' => $rule['buyer_commission_type'],
            'buyer_commission_value' => $rule['buyer_commission_value'],
            
            // Seller/Owner commission
            'seller_commission_ht' => $sellerCommission['ht'],
            'seller_commission_vat' => $sellerCommission['vat'],
            'seller_commission_ttc' => $sellerCommission['ttc'],
            'seller_commission_type' => $rule['seller_commission_type'],
            'seller_commission_value' => $rule['seller_commission_value'],
            
            // Totals
            'total_commission_ht' => $totalCommissionHT,
            'total_commission_vat' => $totalCommissionVAT,
            'total_commission_ttc' => $totalCommissionTTC,
            
            // Splits
            'agent_id' => $agentId,
            'agent_commission_percentage' => $agentPercentage,
            'agent_commission_amount' => round($agentCommissionAmount, 2),
            'agency_commission_amount' => round($agencyCommissionAmount, 2),
            
            // Status
            'payment_status' => 'pending',
            'paid_amount' => 0.00,
            'calculated_by' => session()->get('user_id'),
            'notes' => $transactionData['notes'] ?? null
        ];

        // Step 7: Persist to database if requested
        if ($persist && $transactionId) {
            // Check if already exists
            $existing = $this->commissionModel->getByTransaction($transactionId);
            
            if ($existing) {
                $this->commissionModel->update($existing['id'], $result);
                $commissionId = $existing['id'];
                
                // Log update
                $this->logModel->logAction('commission', $commissionId, 'update', $existing, $result, 'Commission recalculée');
            } else {
                $this->commissionModel->insert($result);
                $commissionId = $this->commissionModel->getInsertID();
                
                // Log creation
                $this->logModel->logAction('commission', $commissionId, 'calculate', null, $result, 'Commission calculée');
            }
            
            $result['id'] = $commissionId;
        }

        return $result;
    }

    /**
     * Get applicable rule with override hierarchy
     * Priority: User > Role > Agency > System Default
     */
    protected function getApplicableRule(int $userId, int $roleId, int $agencyId, string $transactionType, string $propertyType): ?array
    {
        // Try to get override (user > role > agency)
        $override = $this->overrideModel->getBestOverrideForUser($userId, $roleId, $agencyId, $transactionType, $propertyType);
        
        if ($override) {
            // Override found - merge with default rule structure
            $rule = $this->ruleModel->getDefaultRule($transactionType, $propertyType);
            
            if (!$rule) {
                // Si pas de règle par défaut, chercher n'importe quelle règle active pour ce type
                $rule = $this->ruleModel->where([
                    'transaction_type' => $transactionType,
                    'property_type' => $propertyType,
                    'is_active' => 1
                ])->first();
                
                if (!$rule) {
                    throw new \Exception("Aucune règle de commission trouvée pour {$transactionType} - {$propertyType}. Veuillez créer une règle dans les paramètres.");
                }
            }

            // Override values if specified
            if ($override['buyer_commission_type']) {
                $rule['buyer_commission_type'] = $override['buyer_commission_type'];
                $rule['buyer_commission_value'] = $override['buyer_commission_value'];
                $rule['buyer_commission_vat'] = $override['buyer_commission_vat'];
            }
            
            if ($override['seller_commission_type']) {
                $rule['seller_commission_type'] = $override['seller_commission_type'];
                $rule['seller_commission_value'] = $override['seller_commission_value'];
                $rule['seller_commission_vat'] = $override['seller_commission_vat'];
            }

            $rule['override_id'] = $override['id'];
            $rule['override_level'] = $override['override_level'];
            $rule['rule_id'] = $rule['id'];
            
            return $rule;
        }

        // No override - use default system rule
        $rule = $this->ruleModel->getDefaultRule($transactionType, $propertyType);
        
        if (!$rule) {
            // Si pas de règle par défaut, chercher n'importe quelle règle active pour ce type
            $rule = $this->ruleModel->where([
                'transaction_type' => $transactionType,
                'property_type' => $propertyType,
                'is_active' => 1
            ])->first();
            
            if (!$rule) {
                throw new \Exception("Aucune règle de commission trouvée pour {$transactionType} - {$propertyType}. Veuillez créer une règle dans les paramètres.");
            }
        }
        
        if ($rule) {
            $rule['override_level'] = 'system';
            $rule['rule_id'] = $rule['id'];
            $rule['override_id'] = null;
        }
        
        return $rule;
    }

    /**
     * Calculate commission for one party (buyer or seller)
     */
    protected function calculateCommissionForParty(
        float $transactionAmount, 
        string $type, 
        float $value, 
        float $vatRate
    ): array {
        $commissionHT = 0;

        switch ($type) {
            case 'percentage':
                // Commission = X% of transaction amount
                $commissionHT = $transactionAmount * ($value / 100);
                break;

            case 'fixed':
                // Commission = Fixed amount
                $commissionHT = $value;
                break;

            case 'months':
                // Commission = X months of rent (for rentals)
                $commissionHT = $transactionAmount * $value;
                break;
        }

        // Calculate VAT and TTC
        $commissionVAT = $commissionHT * ($vatRate / 100);
        $commissionTTC = $commissionHT + $commissionVAT;

        return [
            'ht' => round($commissionHT, 2),
            'vat' => round($commissionVAT, 2),
            'ttc' => round($commissionTTC, 2),
            'vat_rate' => $vatRate
        ];
    }

    /**
     * Simulate commission (without persisting)
     */
    public function simulateCommission(
        string $transactionType,
        string $propertyType,
        float $transactionAmount,
        int $userId,
        int $roleId,
        int $agencyId
    ): array {
        $transactionData = [
            'transaction_type' => $transactionType,
            'property_type' => $propertyType,
            'amount' => $transactionAmount,
            'property_id' => 0, // Dummy
            'agent_id' => $userId
        ];

        return $this->calculateCommission($transactionData, $userId, $roleId, $agencyId, false);
    }

    /**
     * Validate and approve a commission
     */
    public function validateCommission(int $commissionId, int $validatorId): bool
    {
        $commission = $this->commissionModel->find($commissionId);
        
        if (!$commission) {
            return false;
        }

        $result = $this->commissionModel->update($commissionId, [
            'validated_at' => date('Y-m-d H:i:s'),
            'validated_by' => $validatorId
        ]);

        if ($result) {
            $this->logModel->logAction('commission', $commissionId, 'validate', null, null, 'Commission validée');
        }

        return $result;
    }

    /**
     * Mark commission as paid
     */
    public function markCommissionPaid(int $commissionId, float $paidAmount, int $userId): bool
    {
        $commission = $this->commissionModel->find($commissionId);
        
        if (!$commission) {
            return false;
        }

        $result = $this->commissionModel->markAsPaid($commissionId, $paidAmount, $userId);

        if ($result) {
            $this->logModel->logAction(
                'commission', 
                $commissionId, 
                'payment', 
                ['paid_amount' => $commission['paid_amount']], 
                ['paid_amount' => $paidAmount],
                "Paiement de {$paidAmount} TND enregistré"
            );
        }

        return $result;
    }

    /**
     * Get commission breakdown for display
     */
    public function getCommissionBreakdown(int $commissionId): array
    {
        $commission = $this->commissionModel->find($commissionId);
        
        if (!$commission) {
            return [];
        }

        return [
            'commission' => $commission,
            'breakdown' => [
                'buyer' => [
                    'label' => $commission['transaction_type'] === 'rent' ? 'Locataire' : 'Acheteur',
                    'type' => $commission['buyer_commission_type'],
                    'value' => $commission['buyer_commission_value'],
                    'ht' => $commission['buyer_commission_ht'],
                    'vat' => $commission['buyer_commission_vat'],
                    'ttc' => $commission['buyer_commission_ttc']
                ],
                'seller' => [
                    'label' => $commission['transaction_type'] === 'rent' ? 'Propriétaire' : 'Vendeur',
                    'type' => $commission['seller_commission_type'],
                    'value' => $commission['seller_commission_value'],
                    'ht' => $commission['seller_commission_ht'],
                    'vat' => $commission['seller_commission_vat'],
                    'ttc' => $commission['seller_commission_ttc']
                ],
                'total' => [
                    'ht' => $commission['total_commission_ht'],
                    'vat' => $commission['total_commission_vat'],
                    'ttc' => $commission['total_commission_ttc']
                ],
                'split' => [
                    'agent' => [
                        'percentage' => $commission['agent_commission_percentage'],
                        'amount' => $commission['agent_commission_amount']
                    ],
                    'agency' => [
                        'percentage' => 100 - $commission['agent_commission_percentage'],
                        'amount' => $commission['agency_commission_amount']
                    ]
                ]
            ],
            'rule_info' => [
                'override_level' => $commission['override_level'],
                'rule_id' => $commission['rule_id'],
                'override_id' => $commission['override_id']
            ]
        ];
    }
}
