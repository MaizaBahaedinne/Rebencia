# Extension du Module Property - Guide d'Utilisation

## Vue d'ensemble

L'extension du module Property fournit une architecture complète pour gérer des propriétés avec des données avancées immobilières, incluant:

- 🏠 **Dimensions des pièces** - Surface, orientation, type
- 📍 **Localisation & Scores** - Proximités, sécurité, qualité zone
- 💰 **Données Financières** - Rendements, ROI, valuations
- 📊 **Coûts Estimés** - Charges mensuelles et annuelles
- 🧭 **Orientation & Exposition** - Soleil, vues, expositions
- 🎥 **Multimédia Avancé** - Plans, rendus 3D, vidéos
- ✅ **Options & Équipements** - Piscine, gym, parking, etc.
- ⚙️ **Configuration Admin** - Contrôle des features par type

## Architecture

### Base de Données

**8 nouvelles tables**, entièrement backward-compatible:

1. **property_options** - Catalogue des équipements/options disponibles
2. **property_option_values** - Équipements sélectionnés pour une propriété
3. **property_rooms** - Dimensions des pièces
4. **property_location_scoring** - Scores de localisation et proximités
5. **property_financial_data** - Données investissement et rendement
6. **property_estimated_costs** - Charges estimées mensuel/annuel
7. **property_media_extension** - Plans, rendus 3D, vidéos
8. **property_orientation** - Orientation, exposition, vues
9. **property_admin_config** - Configuration features par type propriété

### Modèles

#### PropertyExtendedModel
Étend fonctionnalités de base avec:
- `getOptions($propertyId)` - Récupérer équipements
- `getRooms($propertyId)` - Récupérer pièces
- `getLocationScoring($propertyId)` - Scores localisation
- `getFinancialData($propertyId)` - Données financières
- `getEstimatedCosts($propertyId)` - Coûts estimés
- `getOrientation($propertyId)` - Orientation/exposition
- `getMediaExtension($propertyId, $type)` - Médias
- `getPropertyComplete($propertyId)` - Tout en une seule requête
- Et 15+ autres méthodes utilitaires

### Services

#### PropertyFinancialService
Calculs financiers avancés:
- `calculateGrossYield()` - Rendement brut
- `calculateNetYield()` - Rendement net
- `calculateCapRate()` - Cap rate
- `calculateROI()` - Return on investment
- `analyzeProperty($propertyId)` - Analyse complète
- `compareProperties($id1, $id2)` - Comparaison
- `calculateInvestmentProjection($propertyId, $years)` - Projections futures

#### PropertyConfigService
Gestion configuration features:
- `getConfig($propertyType)` - Configuration pour type
- `getVisibleSections($propertyType)` - Sections UI à afficher
- `toggleFeature($propertyType, $feature)` - Activer/désactiver
- `validatePropertyData($propertyId)` - Validation contre rules
- `getAvailableOptions($propertyType)` - Options autorisées

#### PropertyCalculationService
Calculs complexes:
- `calculateRoomsTotalSurface($propertyId)` - Surface totale pièces
- `calculateMonthlyExpenses($propertyId)` - Coûts mensuels
- `calculateMinimumRental($propertyId, $margin)` - Loyer minimum
- `calculatePropertyAttractionScore($propertyId)` - Score attractivité (0-100)
- `compareWithMarketAverage($propertyId)` - Comparaison marché
- `getCompleteDashboardStats($propertyId)` - Stats complètes

## Utilisation dans le Contrôleur

### Exemple 1: Récupérer propriété complète

```php
<?php
// Dans PropertyController

public function view($id)
{
    $extendedModel = model(PropertyExtendedModel::class);
    
    // Obtenir tout en une requête
    $property = $extendedModel->getPropertyComplete($id);
    
    if (!$property) {
        return redirect()->to('admin/properties')->with('error', 'Propriété non trouvée');
    }
    
    // Propriété contient maintenant:
    // - property['options'] = array
    // - property['rooms'] = array
    // - property['location_scoring'] = array|null
    // - property['financial_data'] = array|null
    // - property['orientation'] = array|null
    // - property['media_extension'] = array
    // - property['config'] = configuration pour ce type
    
    return view('admin/properties/view', [
        'property' => $property,
        'sectionStats' => $this->getSectionStats($property)
    ]);
}

private function getSectionStats($property)
{
    $configService = service(PropertyConfigService::class);
    $calculationService = service(PropertyCalculationService::class);
    
    return [
        'visible_sections' => $configService->getVisibleSections($property['type']),
        'attraction_score' => $calculationService->calculatePropertyAttractionScore($property['id']),
        'location_score' => $calculationService->getLocationOverallScore($property['id']),
        'monthly_expenses' => $calculationService->calculateMonthlyExpenses($property['id']),
    ];
}
```

### Exemple 2: Analyser rentabilité

```php
<?php
public function analyzeFinancials($propertyId)
{
    $financialService = service(PropertyFinancialService::class);
    
    // Analyse complète
    $analysis = $financialService->analyzeProperty($propertyId);
    
    // Retourner JSON pour dashboard
    return $this->response->setJSON([
        'reference' => $analysis['property_reference'],
        'gross_yield' => $analysis['metrics']['gross_yield'],
        'net_yield' => $analysis['metrics']['net_yield'],
        'cap_rate' => $analysis['metrics']['cap_rate'],
        'payback_period' => $analysis['metrics']['payback_period_years'],
        'annual_expenses' => $analysis['annual_expenses'],
    ]);
}
```

### Exemple 3: Projection d'investissement

```php
<?php
public function investmentProjection($propertyId)
{
    $financialService = service(PropertyFinancialService::class);
    
    // Projection sur 10 ans
    $projection = $financialService->calculateInvestmentProjection($propertyId, 10);
    
    return view('admin/properties/projection', [
        'projection' => $projection,
        'chartData' => json_encode($this->formatForChart($projection))
    ]);
}

private function formatForChart($projection)
{
    $years = [];
    $values = [];
    $appreciation = [];
    
    foreach ($projection['projections'] as $p) {
        $years[] = 'Year ' . $p['year'];
        $values[] = $p['total_profit'];
        $appreciation[] = $p['appreciation_gain'];
    }
    
    return [
        'labels' => $years,
        'datasets' => [
            [
                'label' => 'Total Profit',
                'data' => $values,
                'borderColor' => '#28a745',
            ],
            [
                'label' => 'Appreciation',
                'data' => $appreciation,
                'borderColor' => '#007bff',
            ]
        ]
    ];
}
```

### Exemple 4: Valider données avec règles config

```php
<?php
public function saveDraft($propertyId)
{
    $configService = service(PropertyConfigService::class);
    
    $property = model(PropertyModel::class)->find($propertyId);
    
    // Valider selon config du type
    $validation = $configService->validatePropertyData($propertyId);
    
    if (!$validation['valid']) {
        return $this->response->setJSON([
            'success' => false,
            'errors' => $validation['errors']
        ], 400);
    }
    
    // Propriété valide
    return $this->response->setJSON(['success' => true]);
}
```

### Exemple 5: Chercher propriétés par critères avancés

```php
<?php
public function searchAdvanced()
{
    $extendedModel = model(PropertyExtendedModel::class);
    
    // Chercher propriétés avec excellente localisation
    $goodLocation = $extendedModel->findByLocationScore(75, [
        'proximity_to_schools' => 70,
        'area_safety_score' => 80,
    ]);
    
    // Chercher propriétés avec certaines options
    $withPool = $extendedModel->findByOptions(['pool', 'garden'], 'all');
    
    // Chercher propriétés rentables
    $goodYield = $extendedModel->findByYield(4.5, 'net');
    
    return view('search/results', [
        'goodLocation' => $goodLocation,
        'withPool' => $withPool,
        'goodYield' => $goodYield,
    ]);
}
```

## Utilisation dans les Vues

### Affichage Conditionnel selon Configuration

```php
<?php
// Dans view property details

$configService = service(PropertyConfigService::class);
$visibleSections = $configService->getVisibleSections($property['type']);
?>

<div class="property-tabs">
    <?php if ($visibleSections['rooms']['enabled'] ?? false): ?>
        <button class="tab-button" data-tab="rooms">
            <i class="fa-door-open"></i> Dimensions
        </button>
    <?php endif; ?>
    
    <?php if ($visibleSections['location_scoring']['enabled'] ?? false): ?>
        <button class="tab-button" data-tab="location">
            <i class="fa-map-marker-alt"></i> Localisation
        </button>
    <?php endif; ?>
    
    <?php if ($visibleSections['financial_data']['enabled'] ?? false): ?>
        <button class="tab-button" data-tab="financial">
            <i class="fa-chart-line"></i> Finances
        </button>
    <?php endif; ?>
</div>

<!-- Tab content -->
<?php if ($visibleSections['rooms']['enabled'] ?? false): ?>
<div id="rooms-tab" class="tab-content">
    <h3>Dimensions des Pièces</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Pièce</th>
                <th>Type</th>
                <th>Surface (m²)</th>
                <th>Orientation</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($property['rooms'] ?? [] as $room): ?>
            <tr>
                <td><?= esc($room['name_fr']) ?></td>
                <td><?= esc($room['room_type']) ?></td>
                <td><?= $room['surface'] ?? 'N/A' ?></td>
                <td><?= esc($room['orientation'] ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
```

### Affichage Score d'Attractivité

```php
<?php
$calculationService = service(PropertyCalculationService::class);
$score = $calculationService->calculatePropertyAttractionScore($property['id']);

function getScoreBadge($score) {
    if ($score >= 80) {
        return '<span class="badge bg-success">Excellent (' . $score . ')</span>';
    } elseif ($score >= 60) {
        return '<span class="badge bg-info">Bon (' . $score . ')</span>';
    } elseif ($score >= 40) {
        return '<span class="badge bg-warning">Moyen (' . $score . ')</span>';
    } else {
        return '<span class="badge bg-danger">Faible (' . $score . ')</span>';
    }
}
?>

<div class="alert alert-info">
    Attractivité Globale: <?= getScoreBadge($score) ?>
</div>
```

## Backward Compatibility

✅ **L'extension est 100% backward-compatible**:

- ✅ Aucune modification des tables existantes
- ✅ Aucun changement au PropertyModel
- ✅ Toutes les nouvelles données sont optionnelles
- ✅ Les propriétés existantes fonctionnent sans données étendues
- ✅ Fallback automatique aux valeurs par défaut

```php
<?php
// Code existant continue fonctionner exactement pareil
$properties = model(PropertyModel::class)->getAllWithAgencyFilter();

// Nouvelles données disponibles si présentes
$extendedModel = model(PropertyExtendedModel::class);
$options = $extendedModel->getOptions($propertyId); // array() si aucune option
$rooms = $extendedModel->getRooms($propertyId);    // array() si pas de pièces
```

## Configuration par Type

Activer/désactiver features pour chaque type de propriété:

```php
<?php
// Dans contrôleur admin

public function configureType($type)
{
    $configService = service(PropertyConfigService::class);
    
    if ($this->request->getMethod() === 'post') {
        $config = [
            'enable_rooms' => $this->request->getPost('enable_rooms'),
            'enable_location_scoring' => $this->request->getPost('enable_location_scoring'),
            'enable_financial_data' => $this->request->getPost('enable_financial_data'),
            'required_rooms' => $this->request->getPost('required_rooms'),
            'max_rooms_allowed' => $this->request->getPost('max_rooms_allowed'),
        ];
        
        $configService->saveConfig($type, $config);
        return redirect()->back()->with('success', 'Configuration sauvegardée');
    }
    
    return view('admin/property_config', [
        'config' => $configService->getConfig($type),
        'type' => $type,
    ]);
}
```

## Migrations

Exécuter les migrations:

```bash
php spark migrate
```

Cela créera les 8 nouvelles tables en préservant toutes les données existantes.

Rollback:

```bash
php spark migrate --rollback
```

## Structure de Données Complète

Voir [Property Extension DB Schema](./DB_SCHEMA.md) pour détails complets des tables et relations.

## Prochaines Étapes

1. ✅ Créer migrations
2. ✅ Créer PropertyExtendedModel
3. ✅ Créer services financiers/config/calculs
4. ⏳ Créer PropertyExtendedController
5. ⏳ Créer interfaces admin
6. ⏳ Intégrer dans views existantes
7. ⏳ Ajouter API endpoints
8. ⏳ Tests et validation

