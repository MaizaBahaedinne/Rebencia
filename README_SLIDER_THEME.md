# Système de Gestion des Sliders et du Thème - Installation

## ✅ Fichiers créés

### Migrations
- `app/Database/Migrations/2026-02-09-140000_CreateSlidersTable.php`
- `app/Database/Migrations/2026-02-09-140100_CreateThemeSettingsTable.php`

### Modèles
- `app/Models/SliderModel.php`
- `app/Models/ThemeSettingModel.php`

### Contrôleurs
- `app/Controllers/Admin/Sliders.php`
- `app/Controllers/Admin/Theme.php`

### Vues Admin
- `app/Views/admin/sliders/index.php`
- `app/Views/admin/sliders/create.php`
- `app/Views/admin/sliders/edit.php`
- `app/Views/admin/theme/index.php`

### Composants
- `app/Views/components/slider.php`

### Assets
- `public/assets/css/theme.css`
- `public/uploads/sliders/` (dossier créé)

## 📋 Configuration

### ✅ Routes ajoutées
Les routes suivantes ont été ajoutées dans `app/Config/Routes.php` :

**Sliders :**
- `GET /admin/sliders` - Liste des sliders
- `GET /admin/sliders/create` - Formulaire de création
- `POST /admin/sliders/store` - Enregistrer un slider
- `GET /admin/sliders/edit/:id` - Formulaire d'édition
- `POST /admin/sliders/update/:id` - Mettre à jour
- `POST /admin/sliders/delete/:id` - Supprimer
- `POST /admin/sliders/toggle-status/:id` - Activer/Désactiver

**Thème :**
- `GET /admin/theme` - Interface de personnalisation
- `POST /admin/theme/update` - Enregistrer les modifications
- `GET /admin/theme/reset` - Réinitialiser aux valeurs par défaut
- `POST /admin/theme/preview` - Aperçu en temps réel

### ✅ Menu mis à jour
Le menu admin (`app/Views/admin/partials/sidebar.php`) a été mis à jour avec une nouvelle section "Site Web" contenant :
- **Sliders** - Gestion des sliders animés
- **Thème** - Personnalisation des couleurs et polices
- **Footer** - Gestion du footer (déjà existant)

## 🚀 Installation

### 1. Exécuter les migrations
```bash
php spark migrate
```

### 2. Vérifier les permissions
```bash
chmod 755 public/uploads/sliders
```

### 3. Intégrer le slider dans la page d'accueil
Dans `app/Views/public/home.php` ou votre vue principale :
```php
<?= view('components/slider') ?>
```

### 4. Charger le thème dans le layout
Dans `app/Views/layouts/public.php` (section `<head>`) :
```html
<!-- Polices Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

<!-- Thème personnalisé -->
<link rel="stylesheet" href="<?= base_url('assets/css/theme.css') ?>">
```

## 📱 Accès aux interfaces

Une fois connecté à l'admin :
- **Sliders :** http://votre-site.com/admin/sliders
- **Thème :** http://votre-site.com/admin/theme

## 📚 Documentation complète

Consultez le fichier [GUIDE_SLIDER_THEME.md](GUIDE_SLIDER_THEME.md) pour :
- Guide d'utilisation détaillé
- Explication des fonctionnalités
- Personnalisation CSS
- Structure de la base de données
- Dépannage

## 🎨 Utilisation rapide

### Créer votre premier slider
1. Allez dans **Admin > Sliders**
2. Cliquez sur "Nouveau Slider"
3. Uploadez une image (1920x800px recommandé)
4. Remplissez le titre et la description
5. Configurez l'animation et la position
6. Enregistrez

### Personnaliser le thème
1. Allez dans **Admin > Thème**
2. Sélectionnez vos couleurs avec les color pickers
3. Choisissez vos polices
4. Visualisez l'aperçu en temps réel
5. Cliquez sur "Enregistrer les Modifications"

---

**Dernière mise à jour :** 9 février 2026
