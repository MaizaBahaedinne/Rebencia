# SÉGRÉGATION DES DONNÉES PAR AGENCE

## 📋 Vue d'ensemble

Le système implémente une ségrégation automatique des données basée sur la hiérarchie d'agences :
- **Siège** : Accès à toutes les données de toutes les agences
- **Agence locale** : Accès uniquement à ses données + sous-agences
- **Super admin (niveau ≥ 100)** : Bypasse tous les filtres

## 🏗️ Architecture

### 1. Structure de la base de données

**Table `agencies`** :
- `parent_agency_id` : ID de l'agence parente (NULL pour le siège principal)
- `is_headquarters` : 1 = Siège, 0 = Agence locale

**Exemple de hiérarchie** :
```
Siège National (is_headquarters=1)
 ├─ Agence Paris (parent_agency_id = ID_Siège)
 │   ├─ Agence Paris 15ème (parent_agency_id = ID_Paris)
 │   └─ Agence Paris 8ème (parent_agency_id = ID_Paris)
 ├─ Agence Lyon (parent_agency_id = ID_Siège)
 └─ Agence Marseille (parent_agency_id = ID_Siège)
```

### 2. Fichiers créés

#### Migration
- **`app/Database/Migrations/2026-02-04-140000_AddParentAgencyId.php`**
  - Ajoute `parent_agency_id` et `is_headquarters` à la table `agencies`
  - Crée la clé étrangère pour la hiérarchie

#### Helper
- **`app/Helpers/agency_helper.php`** (auto-chargé globalement)
  
**Fonctions disponibles** :

```php
// Récupère les IDs des agences accessibles par l'utilisateur
$accessibleAgencies = getAccessibleAgencies();
// Retourne: [1, 2, 5, 6] pour un user de l'agence Paris

// Récupère récursivement toutes les sous-agences
$subAgencies = getAllSubAgencies($agencyId, $includeSelf = true);

// Vérifie l'accès à une agence spécifique
if (canAccessAgency($agencyId)) {
    // Autoriser
}

// Vérifie si l'utilisateur est dans un siège
if (isHeadquartersUser()) {
    // Fonctionnalité spéciale siège
}

// Applique automatiquement le filtre d'agence à un builder
$builder = $this->builder();
applyAgencyFilter($builder, 'agency_id');
```

#### Modèles modifiés
- **`app/Models/PropertyModel.php`** :
  - `getPropertyWithDetails()` : Filtre par agence accessible
  - `searchProperties()` : Filtre automatique par agence
  
- **`app/Models/ClientModel.php`** :
  - `getClientWithAgent()` : Filtre par agence accessible
  - `getClientsByStatus()` : Filtre automatique par agence
  - `searchClients()` : Filtre automatique par agence

### 3. Scripts SQL

#### Configuration initiale
- **`setup_agency_hierarchy.sql`** :
  - Ajoute les colonnes `parent_agency_id` et `is_headquarters`
  - Configure le siège principal
  - Exemples de configuration de hiérarchie

## 🚀 Installation

### Étape 1 : Migration de la base de données

Exécutez `setup_agency_hierarchy.sql` dans phpMyAdmin :

```sql
-- Ajoute les colonnes
ALTER TABLE agencies ADD COLUMN parent_agency_id INT(11) UNSIGNED NULL;
ALTER TABLE agencies ADD COLUMN is_headquarters TINYINT(1) DEFAULT 0;

-- Définir le siège principal (remplacer 1 par l'ID réel)
UPDATE agencies SET is_headquarters = 1 WHERE id = 1;
```

### Étape 2 : Configurer la hiérarchie

```sql
-- Exemple: Agence 2 et 3 sont des sous-agences du siège (ID 1)
UPDATE agencies SET parent_agency_id = 1 WHERE id IN (2, 3);

-- Agence 4 est une sous-agence de l'agence 2
UPDATE agencies SET parent_agency_id = 2 WHERE id = 4;
```

### Étape 3 : Vérification

```sql
-- Voir la hiérarchie
SELECT 
    a1.id,
    a1.name AS 'Agence',
    CASE WHEN a1.is_headquarters = 1 THEN 'OUI' ELSE 'NON' END AS 'Siège',
    a2.name AS 'Agence Parente'
FROM agencies a1
LEFT JOIN agencies a2 ON a1.parent_agency_id = a2.id;
```

## 📊 Exemples d'utilisation

### Dans un contrôleur

```php
// Récupérer uniquement les propriétés accessibles
$propertyModel = model('PropertyModel');
$properties = $propertyModel->searchProperties([
    'type' => 'apartment'
]); // Le filtre d'agence est appliqué automatiquement

// Vérifier l'accès avant modification
if (!canAccessAgency($property['agency_id'])) {
    return redirect()->back()->with('error', 'Accès refusé');
}
```

### Dans une vue

```php
<?php if (isHeadquartersUser()): ?>
    <!-- Afficher les statistiques globales -->
    <div class="stats-global">...</div>
<?php else: ?>
    <!-- Afficher les statistiques de l'agence -->
    <div class="stats-agency">...</div>
<?php endif; ?>
```

### Appliquer manuellement le filtre

```php
class CustomModel extends Model
{
    public function getCustomData()
    {
        $builder = $this->db->table('custom_table');
        
        // Appliquer le filtre d'agence
        applyAgencyFilter($builder, 'custom_table.agency_id');
        
        return $builder->get()->getResultArray();
    }
}
```

## 🔐 Règles de sécurité

### Niveaux d'accès

1. **Super Admin (niveau ≥ 100)** :
   - Voit TOUTES les données
   - Pas de filtrage d'agence

2. **Siège (is_headquarters = 1)** :
   - Voit les données du siège
   - + Toutes les sous-agences (récursivement)

3. **Agence locale (is_headquarters = 0)** :
   - Voit uniquement ses données
   - + Ses sous-agences directes

4. **Utilisateur sans agence** :
   - Aucune donnée accessible

### Bypass du filtre

Le filtre est **automatiquement bypassé** pour :
- Super admin (niveau ≥ 100)
- Sessions sans user_id (redirections vers login)

## 🛠️ Maintenance

### Ajouter le filtre à un nouveau modèle

```php
class MyNewModel extends Model
{
    public function getFilteredData($filters = [])
    {
        $builder = $this->builder();
        
        // IMPORTANT: Appliquer le filtre d'agence
        applyAgencyFilter($builder, 'my_table.agency_id');
        
        // Vos filtres personnalisés
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        
        return $builder->get()->getResultArray();
    }
}
```

### Désactiver temporairement le filtre

```php
// Pour des opérations administratives spéciales
if (isSuperAdmin()) {
    // Pas de filtre appliqué
    $allData = $this->db->table('properties')->get()->getResultArray();
}
```

## 📝 Modèles à mettre à jour

Les modèles suivants **ont déjà été modifiés** :
- ✅ PropertyModel
- ✅ ClientModel

Les modèles suivants **nécessitent encore des modifications** :
- ⏳ TransactionModel
- ⏳ UserModel (pour la liste des utilisateurs)
- ⏳ CommissionModel
- ⏳ Tous les autres modèles avec agency_id

## 🔍 Debug

Pour vérifier les agences accessibles par un utilisateur :

```php
// Dans un contrôleur
$accessibleAgencies = getAccessibleAgencies();
var_dump($accessibleAgencies);

// Vérifier si c'est un siège
var_dump(isHeadquartersUser());

// Tester l'accès à une agence spécifique
var_dump(canAccessAgency(5));
```

## ⚠️ Limitations

- Un utilisateur DOIT avoir `agency_id` défini
- La hiérarchie peut avoir une profondeur illimitée (récursion)
- Les performances peuvent être impactées avec beaucoup de niveaux (considérer le caching)
- Le filtre s'applique uniquement aux tables avec `agency_id`

## 🎯 Prochaines étapes

1. Exécuter `setup_agency_hierarchy.sql`
2. Configurer votre hiérarchie d'agences
3. Modifier les autres modèles (Transaction, Commission, etc.)
4. Tester avec différents niveaux d'utilisateurs
5. Ajouter des tests unitaires pour la hiérarchie
