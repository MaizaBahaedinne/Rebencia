# SYSTÈME DE GESTION DES COMMISSIONS - REBENCIA
## Documentation Technique et Guide d'Utilisation

---

## 📋 TABLE DES MATIÈRES

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture du système](#architecture-du-système)
3. [Règles de commission par défaut](#règles-de-commission-par-défaut)
4. [Hiérarchie des surcharges](#hiérarchie-des-surcharges)
5. [Guide d'utilisation](#guide-dutilisation)
6. [Exemples de calcul](#exemples-de-calcul)
7. [API et intégration](#api-et-intégration)
8. [Sécurité et audit](#sécurité-et-audit)

---

## 🎯 VUE D'ENSEMBLE

Le système de gestion des commissions de Rebencia permet de :

✅ Définir des règles de commission par défaut selon le type de transaction et de bien  
✅ Surcharger ces règles au niveau agence, rôle ou utilisateur  
✅ Calculer automatiquement les commissions (HT, TVA, TTC)  
✅ Répartir les commissions entre agent et agence  
✅ Simuler des commissions avant validation  
✅ Tracer toutes les modifications (audit complet)  

---

## 🏗️ ARCHITECTURE DU SYSTÈME

### Base de données (4 tables principales)

```
commission_rules
├── Règles système par défaut
├── Par type de transaction (sale/rent)
└── Par type de bien (apartment, villa, land, etc.)

commission_overrides
├── Surcharges personnalisées
├── Niveau: agency | role | user
└── Priorité: user > role > agency > system

transaction_commissions
├── Commissions calculées
├── Détails acheteur/vendeur
├── Totaux HT/VAT/TTC
└── Répartition agent/agence

commission_logs
└── Journal d'audit complet
```

### Modèles CodeIgniter 4

- **CommissionRuleModel** : Gestion des règles système
- **CommissionOverrideModel** : Gestion des surcharges
- **TransactionCommissionModel** : Commissions calculées
- **CommissionLogModel** : Audit trail

### Service de calcul

**CommissionCalculatorService** :
- Résolution hiérarchique des règles
- Calcul multi-mode (percentage/fixed/months)
- Gestion TVA
- Répartition agent/agence
- Simulation sans persistance

---

## 💰 RÈGLES DE COMMISSION PAR DÉFAUT

### 1️⃣ VENTE DE BIENS IMMOBILIERS

**Appartements, Villas, Maisons, Terrains, Commerciaux, Bureaux**

```
Acheteur : 2% du prix de vente
Vendeur  : 3% du prix de vente
─────────────────────────────────
TOTAL    : 5% du prix de vente
```

**Exemple** :
```
Prix de vente : 300 000 TND

Commission acheteur :
- HT  : 6 000 TND (2%)
- TVA : 1 140 TND (19%)
- TTC : 7 140 TND

Commission vendeur :
- HT  : 9 000 TND (3%)
- TVA : 1 710 TND (19%)
- TTC : 10 710 TND

TOTAL TTC : 17 850 TND
```

### 2️⃣ VENTE DE FONDS DE COMMERCE

```
Acheteur : 5% du prix de vente
Vendeur  : 5% du prix de vente
─────────────────────────────────
TOTAL    : 10% du prix de vente
```

**Exemple** :
```
Prix de vente : 150 000 TND

Commission acheteur :
- HT  : 7 500 TND (5%)
- TVA : 1 425 TND (19%)
- TTC : 8 925 TND

Commission vendeur :
- HT  : 7 500 TND (5%)
- TVA : 1 425 TND (19%)
- TTC : 8 925 TND

TOTAL TTC : 17 850 TND
```

### 3️⃣ LOCATIONS

**Tous types de biens**

```
Locataire    : 1 mois de loyer HT
Propriétaire : 1 mois de loyer HT
────────────────────────────────────
TOTAL        : 2 mois de loyer HT
```

**Exemple** :
```
Loyer mensuel : 1 200 TND

Commission locataire :
- HT  : 1 200 TND (1 mois)
- TVA :   228 TND (19%)
- TTC : 1 428 TND

Commission propriétaire :
- HT  : 1 200 TND (1 mois)
- TVA :   228 TND (19%)
- TTC : 1 428 TND

TOTAL TTC : 2 856 TND
```

---

## 🔄 HIÉRARCHIE DES SURCHARGES

### Ordre de priorité (du plus fort au plus faible)

```
┌─────────────────────────────────────────┐
│  1. UTILISATEUR SPÉCIFIQUE (user)       │  ← Plus haute priorité
│     • Personnalisation par agent        │
│     • Configuration individuelle        │
└─────────────────────────────────────────┘
              ⬇️
┌─────────────────────────────────────────┐
│  2. RÔLE (role)                         │
│     • Par niveau hiérarchique           │
│     • Super Admin, Admin, Manager, etc. │
└─────────────────────────────────────────┘
              ⬇️
┌─────────────────────────────────────────┐
│  3. AGENCE (agency)                     │
│     • Par établissement                 │
│     • Tarifs négociés par agence        │
└─────────────────────────────────────────┘
              ⬇️
┌─────────────────────────────────────────┐
│  4. SYSTÈME (system)                    │  ← Valeur par défaut
│     • Règles globales                   │
│     • Configurées une seule fois        │
└─────────────────────────────────────────┘
```

### Cas d'usage des surcharges

**NIVEAU AGENCE** :
- Agence premium avec tarifs réduits : 1.5% + 2.5% au lieu de 2% + 3%
- Agence nouvelle avec tarifs promotionnels

**NIVEAU RÔLE** :
- Managers : commission fixe de 5000 TND par vente
- Agents juniors : 1% + 2% (taux réduits)

**NIVEAU UTILISATEUR** :
- Agent star : 1% + 2% (fidélisation)
- Négociation individuelle avec un agent expérimenté

---

## 📖 GUIDE D'UTILISATION

### Installation

1. **Exécuter le script SQL** :
```bash
mysql -u root -p rebe_RebenciaDB < database_commission_system.sql
```

2. **Vérifier les permissions** :
- Module `commissions` créé automatiquement
- Permissions assignées aux rôles

3. **Tester l'accès** :
```
https://rebencia.com/admin/commission-settings/rules
```

### Configuration des règles système

#### Créer une nouvelle règle

```php
// Via l'interface admin
/admin/commission-settings/rules/create

// Ou programmatiquement
$ruleModel = new CommissionRuleModel();
$ruleModel->insert([
    'name' => 'Vente Villa Premium',
    'transaction_type' => 'sale',
    'property_type' => 'villa',
    'buyer_commission_type' => 'percentage',
    'buyer_commission_value' => 1.5,
    'buyer_commission_vat' => 19.00,
    'seller_commission_type' => 'percentage',
    'seller_commission_value' => 2.5,
    'seller_commission_vat' => 19.00,
    'is_active' => 1,
    'is_default' => 0
]);
```

### Créer une surcharge

#### Surcharge au niveau agence

```php
$overrideModel = new CommissionOverrideModel();
$overrideModel->upsertOverride([
    'override_level' => 'agency',
    'agency_id' => 5,
    'transaction_type' => 'sale',
    'property_type' => 'apartment',
    'buyer_commission_type' => 'percentage',
    'buyer_commission_value' => 1.5,  // Au lieu de 2%
    'seller_commission_type' => 'percentage',
    'seller_commission_value' => 2.5,  // Au lieu de 3%
    'notes' => 'Tarif négocié pour agence Tunis Centre',
    'created_by' => session()->get('user_id')
]);
```

#### Surcharge au niveau utilisateur

```php
$overrideModel->upsertOverride([
    'override_level' => 'user',
    'user_id' => 42,
    'transaction_type' => 'sale',
    'property_type' => 'villa',
    'buyer_commission_type' => 'percentage',
    'buyer_commission_value' => 1.0,
    'seller_commission_type' => 'percentage',
    'seller_commission_value' => 2.0,
    'notes' => 'Tarif spécial agent star',
    'created_by' => 1
]);
```

### Calculer une commission

```php
use App\Services\CommissionCalculatorService;

$calculator = new CommissionCalculatorService();

$transactionData = [
    'transaction_id' => 123,
    'property_id' => 456,
    'transaction_type' => 'sale',
    'property_type' => 'apartment',
    'amount' => 250000,  // Prix de vente
    'agent_id' => 42,
    'agent_commission_percentage' => 50  // 50% pour l'agent, 50% pour l'agence
];

// Calculer et persister
$result = $calculator->calculateCommission(
    $transactionData,
    userId: 42,
    roleId: 5,
    agencyId: 10,
    persist: true
);

// Résultat
print_r($result);
/*
Array (
    [buyer_commission_ttc] => 5950.00
    [seller_commission_ttc] => 8925.00
    [total_commission_ttc] => 14875.00
    [agent_commission_amount] => 7437.50
    [agency_commission_amount] => 7437.50
    [override_level] => 'user'
    ...
)
*/
```

### Simuler une commission

```php
$simulation = $calculator->simulateCommission(
    transactionType: 'rent',
    propertyType: 'apartment',
    transactionAmount: 1500,  // Loyer mensuel
    userId: 42,
    roleId: 5,
    agencyId: 10
);

// Résultat sans enregistrement en base
print_r($simulation);
```

---

## 🧮 EXEMPLES DE CALCUL

### Exemple 1 : Vente appartement (règle système)

**Données** :
- Type : Vente
- Bien : Appartement
- Prix : 200 000 TND
- Utilisateur : Agent normal (pas de surcharge)

**Règle appliquée** : Système (2% + 3%)

**Calcul** :
```
Commission acheteur :
200 000 × 2% = 4 000 TND HT
TVA 19% = 760 TND
Total TTC = 4 760 TND

Commission vendeur :
200 000 × 3% = 6 000 TND HT
TVA 19% = 1 140 TND
Total TTC = 7 140 TND

TOTAL COMMISSION : 11 900 TND TTC

Répartition (50/50) :
- Agent : 5 950 TND
- Agence : 5 950 TND
```

### Exemple 2 : Vente villa (surcharge utilisateur)

**Données** :
- Type : Vente
- Bien : Villa
- Prix : 500 000 TND
- Utilisateur : Agent star avec surcharge (1% + 2%)

**Règle appliquée** : User (1% + 2%)

**Calcul** :
```
Commission acheteur :
500 000 × 1% = 5 000 TND HT
TVA 19% = 950 TND
Total TTC = 5 950 TND

Commission vendeur :
500 000 × 2% = 10 000 TND HT
TVA 19% = 1 900 TND
Total TTC = 11 900 TND

TOTAL COMMISSION : 17 850 TND TTC

Répartition (60/40 - négocié) :
- Agent : 10 710 TND
- Agence : 7 140 TND
```

### Exemple 3 : Location (règle système)

**Données** :
- Type : Location
- Bien : Appartement
- Loyer : 1 800 TND/mois
- Utilisateur : Agent normal

**Règle appliquée** : Système (1 mois + 1 mois)

**Calcul** :
```
Commission locataire :
1 800 × 1 mois = 1 800 TND HT
TVA 19% = 342 TND
Total TTC = 2 142 TND

Commission propriétaire :
1 800 × 1 mois = 1 800 TND HT
TVA 19% = 342 TND
Total TTC = 2 142 TND

TOTAL COMMISSION : 4 284 TND TTC

Répartition (50/50) :
- Agent : 2 142 TND
- Agence : 2 142 TND
```

### Exemple 4 : Fonds de commerce (règle système)

**Données** :
- Type : Vente
- Bien : Business (fonds de commerce)
- Prix : 100 000 TND
- Utilisateur : Agent normal

**Règle appliquée** : Système (5% + 5%)

**Calcul** :
```
Commission acheteur :
100 000 × 5% = 5 000 TND HT
TVA 19% = 950 TND
Total TTC = 5 950 TND

Commission vendeur :
100 000 × 5% = 5 000 TND HT
TVA 19% = 950 TND
Total TTC = 5 950 TND

TOTAL COMMISSION : 11 900 TND TTC

Répartition (50/50) :
- Agent : 5 950 TND
- Agence : 5 950 TND
```

---

## 🔌 API ET INTÉGRATION

### Intégration dans le workflow de transaction

**Lors de la signature d'un contrat** :

```php
// app/Controllers/Admin/Transactions.php

public function signContract($transactionId)
{
    $transaction = $this->transactionModel->find($transactionId);
    
    // ... validation du contrat ...
    
    // Calculer automatiquement la commission
    $calculator = new CommissionCalculatorService();
    
    $transactionData = [
        'transaction_id' => $transaction['id'],
        'property_id' => $transaction['property_id'],
        'transaction_type' => $transaction['type'],
        'property_type' => $transaction['property_type'],
        'amount' => $transaction['amount'],
        'agent_id' => $transaction['agent_id']
    ];
    
    $user = $this->userModel->find($transaction['agent_id']);
    
    $commission = $calculator->calculateCommission(
        $transactionData,
        $user['id'],
        $user['role_id'],
        $user['agency_id'],
        persist: true
    );
    
    // Notification à l'agent
    $this->notifyAgent($user['id'], $commission);
    
    // Mise à jour du statut de la transaction
    $this->transactionModel->update($transactionId, [
        'status' => 'signed',
        'commission_calculated' => 1
    ]);
}
```

### API REST (pour intégrations externes)

```php
// Routes API
$routes->group('api/v1/commissions', ['filter' => 'api-auth'], function($routes) {
    $routes->get('calculate', 'API\Commissions::calculate');
    $routes->get('transaction/(:num)', 'API\Commissions::getByTransaction/$1');
    $routes->post('validate/(:num)', 'API\Commissions::validate/$1');
});
```

---

## 🔒 SÉCURITÉ ET AUDIT

### Permissions requises

```
commissions_view          : Voir les commissions
commissions_create        : Calculer les commissions
commissions_validate      : Valider les commissions
commissions_edit_rules    : Modifier les règles système (super admin only)
commissions_edit_overrides: Gérer les surcharges (admin+)
commissions_payments      : Enregistrer les paiements
commissions_reports       : Accéder aux rapports avancés
```

### Traçabilité complète

Chaque action est enregistrée dans `commission_logs` :

```php
// Exemple de log automatique
{
    "entity_type": "commission",
    "entity_id": 123,
    "action": "calculate",
    "user_id": 42,
    "user_role": "agent",
    "ip_address": "196.203.XX.XX",
    "old_values": null,
    "new_values": {
        "total_commission_ttc": 17850.00,
        "override_level": "user"
    },
    "description": "Commission calculée",
    "created_at": "2026-02-05 14:30:00"
}
```

### Consulter l'audit trail

```php
$logModel = new CommissionLogModel();

// Logs d'une commission spécifique
$logs = $logModel->getEntityLogs('commission', 123);

// Logs d'un utilisateur
$userLogs = $logModel->getUserLogs(42);

// Logs par période
$logs = $logModel->getLogsByDateRange('2026-02-01', '2026-02-28');
```

---

## 🎓 RÉSUMÉ POUR UTILISATEURS

### Pour les Agents

✅ Vos commissions sont calculées automatiquement lors de la signature  
✅ Consultez vos commissions : `/admin/commissions`  
✅ Utilisez le simulateur avant de négocier : `/admin/commission-settings/simulate`  

### Pour les Managers

✅ Validez les commissions calculées  
✅ Consultez les performances par agent  
✅ Gérez les surcharges pour votre équipe  

### Pour les Administrateurs

✅ Configurez les règles système  
✅ Créez des surcharges par agence/rôle/utilisateur  
✅ Consultez l'audit trail complet  
✅ Exportez les rapports de commission  

---

## ✅ CHECKLIST DE DÉPLOIEMENT

- [ ] Exécuter `database_commission_system.sql`
- [ ] Vérifier les permissions dans la base
- [ ] Tester le simulateur
- [ ] Calculer une commission test
- [ ] Vérifier les logs d'audit
- [ ] Former les utilisateurs
- [ ] Configurer les surcharges si nécessaire

---

## 📞 SUPPORT

Pour toute question ou assistance :
- Documentation technique : Ce fichier
- Logs système : `/admin/commission-settings/logs`
- Support technique : support@rebencia.com

---

**Version** : 1.0  
**Date** : 2026-02-05  
**Auteur** : Rebencia Development Team
