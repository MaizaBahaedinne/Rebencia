<?php

namespace App\Validation;

/**
 * Règles de validation pour les données étendues des propriétés
 */
class PropertyExtendedRules
{
    /**
     * Valider données de pièce
     */
    public function validateRoom(string $str, string $fields, array $data): bool
    {
        if (empty($data['name_fr'])) {
            return false;
        }
        
        if (empty($data['room_type'])) {
            return false;
        }
        
        if (isset($data['surface']) && $data['surface'] < 0) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Valider score de localisation (0-100)
     */
    public function validLocationScore(string $str, string $fields, array $data): bool
    {
        $score = (int)$str;
        return $score >= 0 && $score <= 100;
    }
    
    /**
     * Valider données financières
     */
    public function validateFinancialData(string $str, string $fields, array $data): bool
    {
        if (isset($data['estimated_market_price']) && $data['estimated_market_price'] < 0) {
            return false;
        }
        
        if (isset($data['estimated_rental_price']) && $data['estimated_rental_price'] < 0) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Valider coûts (positifs)
     */
    public function validateCost(string $str): bool
    {
        return (float)$str >= 0;
    }
    
    /**
     * Valider orientation (N/S/E/W)
     */
    public function validateOrientation(string $str): bool
    {
        $valid = ['north', 'south', 'east', 'west', ''];
        return in_array($str, $valid);
    }
    
    /**
     * Valider type de fichier média
     */
    public function validateMediaType(string $str): bool
    {
        $valid = ['floor_plan', '3d_render', 'video_tour', 'drone_photo', 'technical_plan', 'document', 'other'];
        return in_array($str, $valid);
    }
}
