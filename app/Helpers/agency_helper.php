<?php

if (!function_exists('getAccessibleAgencies')) {
    /**
     * Récupérer les IDs des agences accessibles par l'utilisateur
     * - Super admin (niveau >= 100): toutes les agences
     * - Siège: toutes les agences
     * - Agence locale: uniquement son agence + sous-agences
     * 
     * @return array IDs des agences accessibles
     */
    function getAccessibleAgencies(): array
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return [];
        }

        // Récupérer les infos utilisateur avec rôle actif
        $userModel = model('UserModel');
        $user = $userModel->find($userId);
        
        if (!$user) {
            return [];
        }

        // Super admin et directeur siège voient tout
        $activeRole = $userModel->getActiveRole($userId);
        if ($activeRole && $activeRole['level'] >= 90) {
            $agencyModel = model('AgencyModel');
            $allAgencies = $agencyModel->findAll();
            return array_column($allAgencies, 'id');
        }

        // Pas d'agence assignée = accès à rien
        if (!$user['agency_id']) {
            return [];
        }

        $agencyId = $user['agency_id'];
        $db = \Config\Database::connect();
        
        // Vérifier si les colonnes parent_agency_id et is_headquarters existent
        $fieldsQuery = $db->query("SHOW COLUMNS FROM agencies LIKE 'parent_agency_id'");
        $hasHierarchy = $fieldsQuery->getNumRows() > 0;
        
        if (!$hasHierarchy) {
            // Pas de hiérarchie = uniquement son agence
            return [$agencyId];
        }
        
        // Vérifier si l'utilisateur est dans un siège
        $agency = $db->table('agencies')->where('id', $agencyId)->get()->getRowArray();
        
        if (!$agency) {
            return [$agencyId];
        }

        // Si c'est un siège (is_headquarters = 1), accès à toutes les sous-agences
        if (isset($agency['is_headquarters']) && $agency['is_headquarters'] == 1) {
            return getAllSubAgencies($agencyId, true);
        }

        // Sinon, uniquement son agence + ses sous-agences
        return getAllSubAgencies($agencyId, true);
    }
}

if (!function_exists('getAllSubAgencies')) {
    /**
     * Récupérer récursivement toutes les sous-agences d'une agence
     * 
     * @param int $agencyId ID de l'agence parente
     * @param bool $includeSelf Inclure l'agence elle-même
     * @return array IDs des agences
     */
    function getAllSubAgencies(int $agencyId, bool $includeSelf = true): array
    {
        $db = \Config\Database::connect();
        $agencies = $includeSelf ? [$agencyId] : [];
        
        // Récupérer les enfants directs
        $children = $db->table('agencies')
            ->select('id')
            ->where('parent_agency_id', $agencyId)
            ->get()
            ->getResultArray();
        
        foreach ($children as $child) {
            // Ajouter l'enfant
            $agencies[] = $child['id'];
            
            // Récupérer récursivement les sous-enfants
            $subAgencies = getAllSubAgencies($child['id'], false);
            $agencies = array_merge($agencies, $subAgencies);
        }
        
        return array_unique($agencies);
    }
}

if (!function_exists('canAccessAgency')) {
    /**
     * Vérifier si l'utilisateur peut accéder à une agence spécifique
     * 
     * @param int $agencyId ID de l'agence à vérifier
     * @return bool
     */
    function canAccessAgency(int $agencyId): bool
    {
        $accessibleAgencies = getAccessibleAgencies();
        return in_array($agencyId, $accessibleAgencies);
    }
}

if (!function_exists('isHeadquartersUser')) {
    /**
     * Vérifier si l'utilisateur appartient à un siège
     * 
     * @return bool
     */
    function isHeadquartersUser(): bool
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return false;
        }

        $userModel = model('UserModel');
        $user = $userModel->find($userId);
        
        if (!$user || !$user['agency_id']) {
            return false;
        }

        $db = \Config\Database::connect();
        $agency = $db->table('agencies')
            ->where('id', $user['agency_id'])
            ->get()
            ->getRowArray();
        
        return $agency && $agency['is_headquarters'] == 1;
    }
}

if (!function_exists('applyAgencyFilter')) {
    /**
     * Appliquer automatiquement le filtre d'agence à un builder
     * À utiliser dans les modèles
     * 
     * @param object $builder Builder CodeIgniter
     * @param string $agencyColumn Nom de la colonne agency_id (avec préfixe table si nécessaire)
     * @return object Builder modifié
     */
    function applyAgencyFilter($builder, string $agencyColumn = 'agency_id')
    {
        // Super admin voit tout
        $userId = session()->get('user_id');
        if (!$userId) {
            return $builder;
        }

        $userModel = model('UserModel');
        $activeRole = $userModel->getActiveRole($userId);
        
        if ($activeRole && $activeRole['level'] >= 90) {
            return $builder; // Pas de filtre pour super admin et directeur siège
        }

        // Appliquer le filtre d'agences accessibles
        $accessibleAgencies = getAccessibleAgencies();
        
        if (empty($accessibleAgencies)) {
            // Aucune agence accessible = aucun résultat
            $builder->where($agencyColumn, -1);
        } else {
            // Filtrer par agences accessibles
            $builder->whereIn($agencyColumn, $accessibleAgencies);
        }
        
        return $builder;
    }
}
