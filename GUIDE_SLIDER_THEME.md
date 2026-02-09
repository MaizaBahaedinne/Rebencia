# Guide d'utilisation - Système de Gestion des Sliders et du Thème

## 📸 Gestion des Sliders

### Accès
- URL : `/admin/sliders`
- Menu : Administration > Sliders

### Fonctionnalités

#### 1. Créer un Slider
1. Cliquez sur "Nouveau Slider"
2. Remplissez les champs :
   - **Titre** (requis) : Texte principal du slider
   - **Sous-titre** : Texte secondaire
   - **Description** : Texte descriptif
   - **Image** (requise) : Format JPG, PNG, WebP. Max 2 Mo. Recommandé : 1920x800px

3. Configurez les boutons d'action :
   - **Bouton Principal** : Texte + Lien
   - **Bouton Secondaire** : Texte + Lien (optionnel)

4. Paramètres d'animation :
   - **Type d'animation** : Fondu / Glissement / Zoom
   - **Position du texte** : Gauche / Centre / Droite
   - **Opacité de l'overlay** : 0-100% (assombrir l'image de fond)
   - **Ordre d'affichage** : Numéro de séquence
   - **Statut** : Actif/Inactif

#### 2. Modifier un Slider
1. Cliquez sur l'icône d'édition (crayon)
2. Modifiez les champs souhaités
3. Cliquez sur "Mettre à jour"

#### 3. Activer/Désactiver
- Utilisez l'interrupteur dans la colonne "Statut"
- Changement instantané sans rechargement

#### 4. Supprimer un Slider
1. Cliquez sur l'icône de suppression (poubelle)
2. Confirmez la suppression
3. L'image sera supprimée du serveur

### Intégration dans la Page d'Accueil

Pour afficher les sliders sur la page d'accueil, ajoutez ce code dans votre vue :

```php
<?= view('components/slider') ?>
```

### Personnalisation CSS

Les sliders utilisent Bootstrap 5 et des animations CSS personnalisées :
- `.animation-fade` : Effet de fondu
- `.animation-slide` : Effet de glissement
- `.animation-zoom` : Effet de zoom

Vous pouvez personnaliser le style dans `app/Views/components/slider.php`

---

## 🎨 Gestion du Thème

### Accès
- URL : `/admin/theme`
- Menu : Administration > Thème

### Sections de Personnalisation

#### 1. Palette de Couleurs

**Couleur Primaire**
- Utilisée pour : Boutons principaux, liens, éléments d'accentuation
- Format : Code hexadécimal (#RRGGBB)
- Défaut : `#667eea`

**Couleur Secondaire**
- Utilisée pour : Dégradés, éléments complémentaires
- Complémentaire à la couleur primaire
- Défaut : `#764ba2`

**Couleur d'Accent**
- Utilisée pour : Éléments importants, appels à l'action
- Défaut : `#f5576c`

**Texte Sombre**
- Couleur du texte principal
- Défaut : `#2d3748`

**Texte Clair**
- Couleur du texte sur fonds sombres
- Défaut : `#ffffff`

**Fond Clair**
- Couleur d'arrière-plan général
- Défaut : `#f7fafc`

#### 2. Typographie

**Police Primaire**
- Pour les titres (H1-H6)
- Options : Poppins, Roboto, Open Sans, Montserrat, Lato, Raleway, Inter, Nunito
- Défaut : `Poppins`

**Police Secondaire**
- Pour le contenu et le texte principal
- Options : Roboto, Poppins, Open Sans, Lato, Raleway, Inter, Nunito, Merriweather
- Défaut : `Roboto`

**Taille de Base**
- Taille du texte principal
- Options : 14px (Petit), 15px, 16px (Standard), 17px, 18px (Grand)
- Défaut : `16px`

**Rayon de Bordure**
- Arrondi des coins des éléments (boutons, cartes, etc.)
- Options : 0px (Carré), 4px, 8px (Standard), 12px, 16px (Arrondi)
- Défaut : `8px`

### Aperçu en Temps Réel

Le panneau de droite affiche un aperçu instantané de vos modifications :
- Bouton avec couleur primaire
- Texte avec les polices sélectionnées
- Carte avec bordures arrondies
- Palette de couleurs

### Sauvegarder les Modifications

1. Personnalisez les couleurs et la typographie
2. Visualisez l'aperçu en temps réel
3. Cliquez sur "Enregistrer les Modifications"
4. Le fichier CSS sera généré automatiquement
5. Les changements s'appliquent immédiatement sur tout le site

### Réinitialiser le Thème

Pour revenir aux valeurs par défaut :
1. Cliquez sur le bouton "Réinitialiser"
2. Confirmez l'action
3. Le thème retrouvera ses couleurs et polices d'origine

---

## 🔧 Intégration Technique

### Fichiers Importants

**Migrations**
- `app/Database/Migrations/2026-02-09-140000_CreateSlidersTable.php`
- `app/Database/Migrations/2026-02-09-140100_CreateThemeSettingsTable.php`

**Modèles**
- `app/Models/SliderModel.php`
- `app/Models/ThemeSettingModel.php`

**Contrôleurs**
- `app/Controllers/Admin/Sliders.php`
- `app/Controllers/Admin/Theme.php`

**Vues Admin**
- `app/Views/admin/sliders/` (index, create, edit)
- `app/Views/admin/theme/index.php`

**Composants**
- `app/Views/components/slider.php`

**CSS**
- `public/assets/css/theme.css`

**Uploads**
- `public/uploads/sliders/` (images des sliders)

### Exécution des Migrations

```bash
php spark migrate
```

### Variables CSS Générées

Le système génère automatiquement des variables CSS dans `public/assets/css/theme.css` :

```css
:root {
    --theme-primary: #667eea;
    --theme-secondary: #764ba2;
    --theme-accent: #f5576c;
    --theme-text-dark: #2d3748;
    --theme-text-light: #ffffff;
    --theme-bg-light: #f7fafc;
    --font-primary: 'Poppins', sans-serif;
    --font-secondary: 'Roboto', sans-serif;
    --font-size-base: 16px;
    --border-radius: 8px;
}
```

### Charger le Thème dans votre Layout

Ajoutez dans `<head>` de votre layout :

```html
<!-- Polices Google Fonts (selon votre thème) -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

<!-- Thème personnalisé -->
<link rel="stylesheet" href="<?= base_url('assets/css/theme.css') ?>">
```

---

## 📊 Base de Données

### Table `sliders`

| Champ | Type | Description |
|-------|------|-------------|
| id | INT | ID auto-incrémenté |
| title | VARCHAR(255) | Titre du slider |
| subtitle | VARCHAR(255) | Sous-titre |
| description | TEXT | Description |
| image | VARCHAR(255) | Nom du fichier image |
| button1_text | VARCHAR(100) | Texte bouton 1 |
| button1_link | VARCHAR(255) | Lien bouton 1 |
| button2_text | VARCHAR(100) | Texte bouton 2 |
| button2_link | VARCHAR(255) | Lien bouton 2 |
| animation_type | ENUM | fade, slide, zoom |
| text_position | ENUM | left, center, right |
| overlay_opacity | INT | 0-100 |
| display_order | INT | Ordre d'affichage |
| is_active | TINYINT | 1=actif, 0=inactif |
| created_at | DATETIME | Date de création |
| updated_at | DATETIME | Date de modification |

### Table `theme_settings`

| Champ | Type | Description |
|-------|------|-------------|
| id | INT | ID (toujours 1) |
| primary_color | VARCHAR(7) | Code hex couleur primaire |
| secondary_color | VARCHAR(7) | Code hex couleur secondaire |
| accent_color | VARCHAR(7) | Code hex couleur accent |
| text_dark | VARCHAR(7) | Code hex texte sombre |
| text_light | VARCHAR(7) | Code hex texte clair |
| background_light | VARCHAR(7) | Code hex fond clair |
| font_family_primary | VARCHAR(100) | Nom police primaire |
| font_family_secondary | VARCHAR(100) | Nom police secondaire |
| font_size_base | VARCHAR(20) | Taille de base (px) |
| border_radius | VARCHAR(20) | Rayon bordure (px) |
| updated_at | DATETIME | Date modification |

---

## ✅ Checklist de Déploiement

- [ ] Exécuter les migrations : `php spark migrate`
- [ ] Créer le dossier uploads : `mkdir -p public/uploads/sliders`
- [ ] Définir les permissions : `chmod 755 public/uploads/sliders`
- [ ] Vérifier que le fichier `theme.css` existe dans `public/assets/css/`
- [ ] Intégrer le composant slider dans la page d'accueil
- [ ] Charger le fichier `theme.css` dans le layout principal
- [ ] Créer au moins 3 sliders pour tester le carrousel
- [ ] Personnaliser le thème selon votre charte graphique
- [ ] Tester sur mobile et desktop

---

## 🐛 Dépannage

### Les sliders ne s'affichent pas
1. Vérifiez que la migration a été exécutée
2. Assurez-vous qu'au moins un slider est actif
3. Vérifiez l'intégration du composant : `<?= view('components/slider') ?>`

### Les images ne s'affichent pas
1. Vérifiez les permissions du dossier `public/uploads/sliders/`
2. Assurez-vous que les images ont été uploadées correctement
3. Vérifiez le chemin : `base_url('uploads/sliders/' . $slide['image'])`

### Le thème ne s'applique pas
1. Vérifiez que `theme.css` est chargé dans le layout
2. Videz le cache du navigateur
3. Assurez-vous que la migration `theme_settings` a été exécutée
4. Vérifiez que la table contient une ligne avec id=1

### Erreur 404 sur les routes admin
1. Vérifiez que les routes sont bien définies dans `app/Config/Routes.php`
2. Nettoyez le cache : `php spark cache:clear`

---

## 📞 Support

Pour toute question ou problème, consultez :
- La documentation CodeIgniter 4 : https://codeigniter.com/user_guide/
- La documentation Bootstrap 5 : https://getbootstrap.com/docs/5.0/

---

Dernière mise à jour : <?= date('d/m/Y') ?>
