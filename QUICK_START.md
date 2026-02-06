# 🚀 Quick Start - Extension Property Module

**Démarrer en 5 minutes avec l'extension Property Module.**

## 1️⃣ Installer les Migrations

```bash
# Créer les 9 nouvelles tables en base de données
php spark migrate

# Vérifier les migrations
php spark migrate --show
```

✅ **9 tables créées:** options, option_values, rooms, location_scoring, financial_data, estimated_costs, media_extension, orientation, admin_config

## 2️⃣ Utiliser dans un Contrôleur

```php
<?php
namespace App\Controllers\Admin;

use App\Models\PropertyExtendedModel;
use App\Services\PropertyFinancialService;
use App\Services\PropertyConfigService;
use App\Services\PropertyCalculationService;

class Properties extends BaseController
{
    public function view($id)
    {
        // Récupérer propriété avec TOUTES les données avancées
        $extended = model(PropertyExtendedModel::class);
        $property = $extended->getPropertyComplete($id);
        
        // Propriété inclut maintenant:
        // - property['options'] = array d'équipements
        // - property['rooms'] = array de pièces avec surfaces
        // - property['location_scoring'] = scores localisation
        // - property['financial_data'] = rendements, ROI, etc.
        // - property['estimated_costs'] = charges mensuelles/annuelles
        // - property['orientation'] = exposition, vues
        // - property['media_extension'] = plans, rendus 3D
        // - property['config'] = configuration pour ce type
        
        return view('properties/view', ['property' => $property]);
    }
    
    public function analyze($id)
    {
        // Analyser rentabilité complète
        $financial = service(PropertyFinancialService::class);
        $analysis = $financial->analyzeProperty($id);
        
        // Retour: gross_yield, net_yield, cap_rate, price_per_sqm, 
        //         payback_period, annual_expenses, etc.
        
        return $this->response->setJSON($analysis);
    }
    
    public function dashboard($id)
    {
        // Dashboard investisseur complet
        $calc = service(PropertyCalculationService::class);
        $stats = $calc->getCompleteDashboardStats($id);
        
        return view('properties/investment_dashboard', ['stats' => $stats]);
    }
}
```

## 3️⃣ Utiliser dans une Vue

```php
<!-- app/Views/admin/properties/view.php -->

<?php
$extended = model(\App\Models\PropertyExtendedModel::class);
$calc = service(\App\Services\PropertyCalculationService::class);
?>

<!-- Afficher équipements -->
<?php if (!empty($property['options'])): ?>
    <h3>Équipements</h3>
    <div class="options-list">
    <?php foreach ($property['options'] as $option): ?>
        <span class="badge">
            <i class="fa <?= $option['icon'] ?>"></i>
            <?= $option['name_fr'] ?>
        </span>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Afficher pièces -->
<?php if (!empty($property['rooms'])): ?>
    <h3>Pièces (<?= count($property['rooms']) ?>)</h3>
    <table class="table">
        <tbody>
        <?php foreach ($property['rooms'] as $room): ?>
            <tr>
                <td><?= $room['name_fr'] ?></td>
                <td><?= $room['surface'] ?> m²</td>
                <td><?= $room['room_type'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- Afficher scores localisation -->
<?php if (!empty($property['location_scoring'])): ?>
    <h3>Localisation</h3>
    Score: <strong><?= $property['location_scoring']['overall_location_score'] ?>/100</strong>
    <ul>
        <li>Écoles: <?= $property['location_scoring']['proximity_to_schools'] ?></li>
        <li>Transports: <?= $property['location_scoring']['proximity_to_transport'] ?></li>
        <li>Sécurité: <?= $property['location_scoring']['area_safety_score'] ?></li>
    </ul>
<?php endif; ?>

<!-- Afficher score d'attractivité global -->
<?php
$attraction = $calc->calculatePropertyAttractionScore($property['id']);
$scoreColor = $attraction >= 80 ? 'success' : ($attraction >= 60 ? 'info' : 'warning');
?>
<div class="alert alert-<?= $scoreColor ?>">
    <strong>Score Attractivité:</strong> <?= $attraction ?>/100
</div>
```

## 4️⃣ Services Rapides

### Analyser Rendement
```php
$financial = service(\App\Services\PropertyFinancialService::class);

$analysis = $financial->analyzeProperty($propertyId);
echo "Rendement Net: " . $analysis['metrics']['net_yield'] . "%";
echo "ROI Annuel: " . $analysis['metrics']['roi_annual'] . "%";
echo "Cap Rate: " . $analysis['metrics']['cap_rate'] . "%";
```

### Comparer Deux Propriétés
```php
$financial = service(\App\Services\PropertyFinancialService::class);

$comparison = $financial->compareProperties($property1Id, $property2Id);
// Retour: financial data, comparison, better_value, etc.
```

### Projections d'Investissement
```php
$financial = service(\App\Services\PropertyFinancialService::class);

$projection = $financial->calculateInvestmentProjection($propertyId, 10);
// Projections année par année avec valeur estimée, appréciation, etc.
```

### Configuration par Type
```php
$config = service(\App\Services\PropertyConfigService::class);

// Quelle sections afficher pour 'apartment'?
$sections = $config->getVisibleSections('apartment');

// Feature activée?
if ($config->isFeatureEnabled('villa', 'financial_data')) {
    // Afficher tab finances
}

// Valider propriété avant publication
$validation = $config->validatePropertyData($propertyId);
if ($validation['valid']) {
    // OK pour publier
} else {
    // Afficher erreurs
    foreach ($validation['errors'] as $error) {
        echo "Erreur: $error";
    }
}
```

### Calculs Complexes
```php
$calc = service(\App\Services\PropertyCalculationService::class);

// Score d'attractivité global (0-100)
$score = $calc->calculatePropertyAttractionScore($propertyId);

// Coûts mensuels détaillés
$breakdown = $calc->getMonthlyExpensesBreakdown($propertyId);
echo "Syndic: " . $breakdown['syndic'];
echo "Électricité: " . $breakdown['electricity'];

// Loyer minimum recommandé (avec 20% marge)
$minRental = $calc->calculateMinimumRental($propertyId, 20);

// Comparaison vs marché
$comparison = $calc->compareWithMarketAverage($propertyId);
echo "Status: " . $comparison['status']; // overpriced/fair_value/underpriced
```

## 5️⃣ Recherche Avancée

```php
$extended = model(\App\Models\PropertyExtendedModel::class);

// Propriétés avec certains équipements
$withPool = $extended->findByOptions(['pool', 'garden'], 'all');

// Propriétés avec excellente localisation
$goodLocation = $extended->findByLocationScore(75, [
    'proximity_to_schools' => 70,
    'area_safety_score' => 80
]);

// Propriétés locatives rentables
$goodYield = $extended->findByYield(4.5, 'net');
```

## 6️⃣ Accéder à Données Spécifiques

```php
$extended = model(\App\Models\PropertyExtendedModel::class);

// Options/équipements
$options = $extended->getOptions($propertyId);

// Pièces avec dimensions
$rooms = $extended->getRooms($propertyId);
$totalSurface = $extended->getRoomsTotalSurface($propertyId);

// Localisation et proximités
$location = $extended->getLocationScoring($propertyId);

// Données financières
$financial = $extended->getFinancialData($propertyId);

// Coûts estimés
$costs = $extended->getEstimatedCosts($propertyId);
$monthlyCosts = $extended->getTotalMonthlyCosts($propertyId);

// Orientation et exposition
$orientation = $extended->getOrientation($propertyId);

// Plans d'étage
$floorPlans = $extended->getFloorPlans($propertyId);

// Rendus 3D
$renders = $extended->get3DRenders($propertyId);

// Vidéos
$videos = $extended->getVideoTours($propertyId);

// Résumé complet pour investisseur
$summary = $extended->getInvestorSummary($propertyId);
```

## 💡 Cas d'Usage Courants

### 1. Ajouter Info Rendement à Liste Propriétés
```php
// Dans boucle des propriétés
$extended = model(PropertyExtendedModel::class);
foreach ($properties as &$prop) {
    $prop['estimated_yield'] = $extended->estimateNetYield(
        $prop['id'], 
        $prop['rental_price']
    );
}
```

### 2. Score Qualité pour Dashboard
```php
$calc = service(PropertyCalculationService::class);

$properties = model(PropertyModel::class)->findAll();

foreach ($properties as &$prop) {
    $prop['quality_score'] = $calc->calculatePropertyAttractionScore($prop['id']);
    $prop['location_score'] = $calc->getLocationOverallScore($prop['id']);
}

usort($properties, fn($a, $b) => $b['quality_score'] <=> $a['quality_score']);
```

### 3. Valider Avant Publication
```php
$config = service(PropertyConfigService::class);

if ($this->request->getMethod() === 'post') {
    $validation = $config->validatePropertyData($propertyId);
    
    if (!$validation['valid']) {
        return redirect()->back()->withInput()
            ->with('errors', $validation['errors']);
    }
    
    // Publier
}
```

### 4. Ajouter Colonne "Rendement" à Admin List
```php
// Dans la vue
<?php
$extended = model(PropertyExtendedModel::class);
?>

<table>
    <tr>
        <th>Référence</th>
        <th>Titre</th>
        <th>Prix</th>
        <th>Loyer</th>
        <th>Rendement</th>
    </tr>
    <?php foreach ($properties as $p): ?>
        <tr>
            <td><?= $p['reference'] ?></td>
            <td><?= $p['title'] ?></td>
            <td><?= $p['price'] ?> TND</td>
            <td><?= $p['rental_price'] ?> TND</td>
            <td>
                <?= round($extended->estimateNetYield($p['id'], $p['rental_price']), 2) ?>%
            </td>
        </tr>
    <?php endforeach; ?>
</table>
```

## 📖 Documentation Complète

| Document | Contenu |
|----------|---------|
| `PROPERTY_EXTENSION_GUIDE.md` | Guide utilisateur détaillé |
| `PROPERTY_EXTENSION_DB_SCHEMA.md` | Schéma BD et relations |
| `PROPERTY_EXTENSION_EXAMPLES.php` | 10 cas d'usage réels |
| `PROPERTY_EXTENSION_README.md` | Récapitulatif complet |
| `PROPERTY_EXTENSION_INVENTORY.md` | Inventaire fichiers |
| `PROPERTY_EXTENSION_IMPLEMENTATION_CHECKLIST.md` | Checklist intégration |

## 🧭 Guide Utilisateur (Module Property Extension)

### 1. Accès & Pré-requis
- Être connecté avec un rôle autorisé.
- Les migrations doivent être appliquées (✅ déjà fait).
- Les sections visibles dépendent du type de bien et de sa configuration.

### 2. Configurer les sections par type de bien
- Ouvrir la page de configuration du module (Admin → Propriétés → Configuration).
- Activer ou désactiver les sections : pièces, options, localisation, finances, coûts, orientation, médias.
- Définir les champs obligatoires avant publication.

### 3. Saisir les données étendues d’un bien
Dans la fiche d’un bien, ouvrir l’onglet “Données étendues” :

**Pièces**
- Ajouter les pièces (type, nom, surface).
- Le total des surfaces se calcule automatiquement.

**Options / Équipements**
- Cocher les équipements disponibles (piscine, parking, sécurité, etc.).

**Localisation**
- Renseigner les scores de proximité (écoles, transports, santé, commerces...).
- Le score global se met à jour en temps réel.

**Financier**
- Indiquer prix d’achat, loyer estimé, charges.
- Rendement brut/net, cap rate, prix/m² et ROI sont calculés automatiquement.

**Coûts estimés**
- Saisir les charges mensuelles/annuelles.
- Le total mensuel et annuel est mis à jour automatiquement.

**Orientation & Exposition**
- Choisir l’orientation principale et l’exposition au soleil.

**Médias**
- Ajouter plans, rendus 3D, vidéos (selon configuration).
- Possibilité de supprimer un média à tout moment.

### 4. Valider avant publication
- Le module peut exiger certaines sections avant publication.
- Si des champs obligatoires manquent, un message d’erreur s’affiche.

### 5. Consulter l’analyse investisseur
- Ouvrir l’onglet “Analyse”.
- Visualiser : rendement net, cap rate, prix/m², période de retour.
- Voir l’attractivité globale et la comparaison marché.

### 6. Résolution rapide de problèmes
- **Une section n’apparaît pas** : vérifier la configuration du type de bien.
- **Calculs à zéro** : vérifier que prix/loyer/surface sont renseignés.
- **Accès refusé** : vérifier les permissions du rôle (RBAC).

## ✅ Next Steps

1. **Déployer:** `php spark migrate`
2. **Tester:** Utiliser les exemples ci-dessus
3. **Intégrer:** Ajouter dans vos contrôleurs et vues
4. **Étendre:** Créer des contrôleurs pour les nouvelles données
5. **Optimiser:** Ajouter UI, validations, permissions RBAC

---

**Prêt?** Commencez par `php spark migrate` puis testez avec les exemples ci-dessus! 🚀

