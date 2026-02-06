# Extension Property Module - Schéma Base de Données

## Vue d'ensemble des Relations

```
properties (existante)
    ↓
    ├── property_options ← property_option_values
    ├── property_rooms
    ├── property_location_scoring
    ├── property_financial_data
    ├── property_estimated_costs
    ├── property_media_extension
    ├── property_orientation
    └── property_admin_config (par type)
```

## Détail des Tables

### 1. property_options
**Catalogue de tous les équipements/options disponibles**

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | INT unsigned | PRIMARY KEY | Identifiant |
| code | VARCHAR(50) | UNIQUE NOT NULL | Code unique (air_conditioning, pool, etc.) |
| name_fr | VARCHAR(100) | NOT NULL | Nom français |
| name_ar | VARCHAR(100) | NULL | Nom arabe |
| name_en | VARCHAR(100) | NULL | Nom anglais |
| description | TEXT | NULL | Description détaillée |
| icon | VARCHAR(50) | NULL | Class Font Awesome (fa-swimming-pool, etc.) |
| category | ENUM | NOT NULL | Catégorie (comfort, outdoor, parking, security, amenities, other) |
| is_active | TINYINT | DEFAULT 1 | Option active/inactive |
| sort_order | INT | DEFAULT 0 | Ordre d'affichage |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Création |
| updated_at | TIMESTAMP | AUTO UPDATE | Modification |

**Index**: code (UNIQUE)

### 2. property_option_values
**Junction table: Équipements sélectionnés pour chaque propriété**

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | INT unsigned | PRIMARY KEY | Identifiant |
| property_id | INT unsigned | FK → properties.id CASCADE | Propriété |
| option_id | INT unsigned | FK → property_options.id CASCADE | Option |
| value | VARCHAR(255) | NULL | Valeur optionnelle (ex: 4 pour 4 places) |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Sélection |

**Unique Index**: (property_id, option_id) - Empêche doublons

### 3. property_rooms
**Dimensions détaillées des pièces**

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | INT unsigned | PRIMARY KEY | Identifiant |
| property_id | INT unsigned | FK → properties.id CASCADE | Propriété |
| name_fr | VARCHAR(100) | NOT NULL | Nom pièce (Chambre 1, Salon, etc.) |
| name_ar | VARCHAR(100) | NULL | Nom en arabe |
| room_type | ENUM | NOT NULL DEFAULT 'other' | Type (bedroom, bathroom, kitchen, living, dining, office, storage, utility, other) |
| surface | DECIMAL(10,2) | NULL | Surface en m² |
| width | DECIMAL(10,2) | NULL | Largeur en m |
| length | DECIMAL(10,2) | NULL | Longueur en m |
| height | DECIMAL(10,2) | NULL | Hauteur sous plafond en m |
| has_window | TINYINT | DEFAULT 1 | Possession fenêtre |
| window_type | ENUM | NULL | Type fenêtre (single, double, bay, french_door) |
| orientation | ENUM | NULL | Orientation (N, NE, E, SE, S, SW, W, NW) |
| sort_order | INT | DEFAULT 0 | Ordre affichage |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Création |
| updated_at | TIMESTAMP | AUTO UPDATE | Modification |

**Indexes**: property_id

### 4. property_location_scoring
**Scores de localisation et proximités**

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | INT unsigned | PRIMARY KEY | Identifiant |
| property_id | INT unsigned | FK → properties.id CASCADE UNIQUE | Propriété (1 seul) |
| proximity_to_schools | INT | DEFAULT 0 | Score 0-100 écoles |
| proximity_to_transport | INT | DEFAULT 0 | Score transports publics |
| proximity_to_shopping | INT | DEFAULT 0 | Score commerces |
| proximity_to_parks | INT | DEFAULT 0 | Score parcs |
| proximity_to_healthcare | INT | DEFAULT 0 | Score hôpitaux |
| proximity_to_restaurants | INT | DEFAULT 0 | Score restaurants |
| proximity_to_entertainment | INT | DEFAULT 0 | Score loisirs |
| area_safety_score | INT | DEFAULT 0 | Score sécurité quartier |
| area_noise_level | ENUM | NULL | Bruit (very_quiet, quiet, moderate, noisy, very_noisy) |
| area_cleanliness_score | INT | DEFAULT 0 | Score propreté |
| overall_location_score | INT | DEFAULT 0 | Score global (calculé) |
| location_notes | TEXT | NULL | Notes texte |
| latitude | DECIMAL(11,8) | NULL | Latitude géolocalisation |
| longitude | DECIMAL(11,8) | NULL | Longitude géolocalisation |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Création |
| updated_at | TIMESTAMP | AUTO UPDATE | Modification |

**Indexes**: property_id (UNIQUE)

### 5. property_financial_data
**Données financières et d'investissement**

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | INT unsigned | PRIMARY KEY | Identifiant |
| property_id | INT unsigned | FK → properties.id CASCADE UNIQUE | Propriété |
| estimated_market_price | DECIMAL(15,2) | NULL | Estimation prix marché |
| estimated_rental_price | DECIMAL(10,2) | NULL | Estimation loyer mensuel |
| gross_yield | DECIMAL(5,2) | NULL | Rendement brut % |
| net_yield | DECIMAL(5,2) | NULL | Rendement net % |
| price_per_sqm | DECIMAL(10,2) | NULL | Prix par m² |
| cap_rate | DECIMAL(5,2) | NULL | Cap rate % |
| cash_on_cash_return | DECIMAL(5,2) | NULL | Cash-on-cash % |
| roi_annual | DECIMAL(5,2) | NULL | ROI annuel % |
| payback_period_years | DECIMAL(5,1) | NULL | Amortissement années |
| appreciation_rate | DECIMAL(5,2) | NULL | Taux appréciation annuelle % |
| annual_appreciation_value | DECIMAL(15,2) | NULL | Valeur appréciation annuelle |
| debt_service_ratio | DECIMAL(5,2) | NULL | Ratio couverture dette |
| investor_notes | TEXT | NULL | Notes investisseur |
| last_valuation_date | TIMESTAMP | NULL | Date dernière valuation |
| valuation_method | ENUM | NULL | Méthode valuation (comparable_sales, income_approach, cost_approach, appraisal, automated_valuation) |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Création |
| updated_at | TIMESTAMP | AUTO UPDATE | Modification |

**Indexes**: property_id (UNIQUE)

### 6. property_estimated_costs
**Coûts estimés - charges mensuelles et annuelles**

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | INT unsigned | PRIMARY KEY | Identifiant |
| property_id | INT unsigned | FK → properties.id CASCADE UNIQUE | Propriété |
| syndic_monthly | DECIMAL(10,2) | NULL | Frais syndic mensuel |
| electricity_monthly | DECIMAL(10,2) | NULL | Électricité mensuelle |
| water_monthly | DECIMAL(10,2) | NULL | Eau mensuelle |
| gas_monthly | DECIMAL(10,2) | NULL | Gaz mensuel |
| heating_monthly | DECIMAL(10,2) | NULL | Chauffage mensuel |
| property_tax_annual | DECIMAL(10,2) | NULL | Taxe foncière annuelle |
| income_tax_annual | DECIMAL(10,2) | NULL | Impôt revenus annuel |
| insurance_annual | DECIMAL(10,2) | NULL | Assurance annuelle |
| maintenance_annual | DECIMAL(10,2) | NULL | Maintenance annuelle |
| hoa_fees_monthly | DECIMAL(10,2) | NULL | Frais HOA/association |
| other_costs_monthly | DECIMAL(10,2) | NULL | Autres coûts mensuels |
| total_monthly_costs | DECIMAL(10,2) | NULL | Total mensuel (calculé) |
| total_annual_costs | DECIMAL(15,2) | NULL | Total annuel (calculé) |
| cost_notes | TEXT | NULL | Notes |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Création |
| updated_at | TIMESTAMP | AUTO UPDATE | Modification |

**Indexes**: property_id (UNIQUE)

### 7. property_media_extension
**Fichiers multimédia avancés - plans, rendus 3D, vidéos**

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | INT unsigned | PRIMARY KEY | Identifiant |
| property_id | INT unsigned | FK → properties.id CASCADE | Propriété |
| file_type | ENUM | NOT NULL DEFAULT 'other' | Type (floor_plan, 3d_render, video_tour, drone_photo, technical_plan, document, other) |
| title | VARCHAR(255) | NULL | Titre du fichier |
| description | TEXT | NULL | Description |
| file_path | VARCHAR(500) | NOT NULL | Chemin du fichier |
| file_size | INT | NULL | Taille en octets |
| file_mime | VARCHAR(100) | NULL | Type MIME |
| thumbnail_path | VARCHAR(500) | NULL | Chemin miniature |
| floor_plan_room_count | INT | NULL | Nombre pièces (pour plan) |
| floor_number | INT | NULL | Numéro étage représenté |
| is_primary | TINYINT | DEFAULT 0 | Fichier principal du type |
| is_published | TINYINT | DEFAULT 1 | Publié/visible |
| sort_order | INT | DEFAULT 0 | Ordre d'affichage |
| view_count | INT | DEFAULT 0 | Nombre de vues |
| uploaded_by | INT unsigned | FK → users.id SET NULL | Utilisateur upload |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Création |
| updated_at | TIMESTAMP | AUTO UPDATE | Modification |

**Indexes**: property_id, file_type, uploaded_by

### 8. property_orientation
**Orientation, exposition au soleil, vues**

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | INT unsigned | PRIMARY KEY | Identifiant |
| property_id | INT unsigned | FK → properties.id CASCADE UNIQUE | Propriété |
| primary_orientation | ENUM | NULL | Orientation principale (N, NE, E, SE, S, SW, W, NW) |
| secondary_orientation | ENUM | NULL | Orientation secondaire |
| sun_exposure | ENUM | NULL | Exposition soleil (none, morning, afternoon, all_day, partial) |
| morning_sun | TINYINT | DEFAULT 0 | Soleil matin |
| afternoon_sun | TINYINT | DEFAULT 0 | Soleil après-midi |
| evening_sun | TINYINT | DEFAULT 0 | Soleil soir |
| view_type | ENUM | NULL | Type de vue (garden, street, water, mountain, city, park, courtyard, none) |
| view_quality | ENUM | NULL | Qualité vue (poor, average, good, excellent) |
| natural_light_level | ENUM | NULL | Luminosité (minimal, moderate, bright, very_bright) |
| has_balcony_or_terrace | TINYINT | DEFAULT 0 | Possession balcon/terrasse |
| balcony_orientation | ENUM | NULL | Orientation balcon |
| balcony_surface | DECIMAL(10,2) | NULL | Surface balcon en m² |
| wind_exposure | ENUM | NULL | Exposition vent (sheltered, moderate, exposed) |
| privacy_level | ENUM | NULL | Intimité (none, partial, good, excellent) |
| orientation_notes | TEXT | NULL | Notes |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Création |
| updated_at | TIMESTAMP | AUTO UPDATE | Modification |

**Indexes**: property_id (UNIQUE)

### 9. property_admin_config
**Configuration admin - features par type de propriété**

| Colonne | Type | Contraintes | Description |
|---------|------|-------------|-------------|
| id | INT unsigned | PRIMARY KEY | Identifiant |
| property_type | ENUM | NOT NULL UNIQUE | Type propriété (apartment, villa, house, land, office, commercial, warehouse, other) |
| enable_rooms | TINYINT | DEFAULT 1 | Afficher tab pièces |
| enable_location_scoring | TINYINT | DEFAULT 1 | Afficher localisation |
| enable_financial_data | TINYINT | DEFAULT 1 | Afficher finances |
| enable_estimated_costs | TINYINT | DEFAULT 1 | Afficher coûts |
| enable_orientation | TINYINT | DEFAULT 1 | Afficher orientation |
| enable_media_extension | TINYINT | DEFAULT 1 | Afficher médias |
| enable_options | TINYINT | DEFAULT 1 | Afficher options |
| required_rooms | TINYINT | DEFAULT 0 | Pièces obligatoires |
| required_location_scoring | TINYINT | DEFAULT 0 | Localisation obligatoire |
| required_financial_data | TINYINT | DEFAULT 0 | Finances obligatoires |
| required_estimated_costs | TINYINT | DEFAULT 0 | Coûts obligatoires |
| required_orientation | TINYINT | DEFAULT 0 | Orientation obligatoire |
| allowed_option_categories | VARCHAR(500) | NULL | JSON array catégories autorisées |
| max_rooms_allowed | INT | NULL | Max pièces à entrer |
| default_valuation_method | ENUM | NULL | Méthode valuation défaut |
| show_roi_metrics | TINYINT | DEFAULT 1 | Afficher ROI |
| show_on_listings | TINYINT | DEFAULT 1 | Visible annonces publiques |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Création |
| updated_at | TIMESTAMP | AUTO UPDATE | Modification |

**Indexes**: property_type (UNIQUE)

## Relations et Intégrité Référentielle

Toutes les clés étrangères utilisent:
- `CASCADE ON DELETE` - Supprimer données liées si propriété supprimée
- `CASCADE ON UPDATE` - Mettre à jour si ID propriété change

```sql
-- Exemples de cascades
FOREIGN KEY (property_id) REFERENCES properties(id) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
```

## Considérations de Performance

### Indexes recommandés pour requêtes fréquentes:

```sql
-- Recherche par localisation
CREATE INDEX idx_location_overall_score 
ON property_location_scoring(overall_location_score DESC);

-- Recherche par rendement
CREATE INDEX idx_financial_yield 
ON property_financial_data(net_yield DESC);

-- Recherche par options
CREATE INDEX idx_option_values_property_option 
ON property_option_values(property_id, option_id);

-- Recherche par date
CREATE INDEX idx_rooms_created 
ON property_rooms(property_id, created_at DESC);
```

## Données par Défaut

Aucune donnée par défaut n'est insérée. Les propriétés existantes continuent fonctionner sans données étendues.

## Volumétrie Estimée

Pour une plateforme avec 10,000 propriétés:
- property_options: 50-100 enregistrements
- property_option_values: ~5-10 options/propriété = 50-100k
- property_rooms: ~5 pièces/propriété = 50k
- property_location_scoring: 1/propriété = 10k (environ 30% remplissage)
- property_financial_data: 1/propriété = 10k (environ 20% remplissage)
- property_estimated_costs: 1/propriété = 10k (environ 15% remplissage)
- property_media_extension: ~3-5 fichiers/propriété = 30-50k
- property_orientation: 1/propriété = 10k (environ 40% remplissage)
- property_admin_config: 8 enregistrements (types propriété)

**Total estimé**: ~200-300k enregistrements supplémentaires

## Migration Depuis Autres Systèmes

Pour importer depuis autre système:
1. Préparer données CSV/SQL
2. Écrire script d'import PHP utilisant les modèles
3. Utiliser transactions pour garantir intégrité
4. Valider relations avec `validatePropertyData()`

Exemple:
```php
// Dans migration de données personnalisée
$propertyModel = model(PropertyExtendedModel::class);
$configService = service(PropertyConfigService::class);

foreach ($importedData as $propertyData) {
    // Insérer données
    // ...
    
    // Valider
    $validation = $configService->validatePropertyData($propertyId);
}
```

