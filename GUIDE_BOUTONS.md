# Personnalisation des Boutons - Guide Complet

## 🎨 Nouveaux Paramètres Ajoutés

Vous pouvez maintenant personnaliser **9 aspects** du design des boutons depuis `/admin/theme` :

### Couleurs
1. **Couleur Fond** - Couleur de fond du bouton (ex: #667eea)
2. **Couleur Texte** - Couleur du texte du bouton (ex: #ffffff)
3. **Fond Survol** - Couleur de fond au survol (ex: #764ba2)
4. **Texte Survol** - Couleur du texte au survol (ex: #ffffff)
5. **Couleur Bordure** - Couleur de la bordure si activée

### Style
6. **Largeur Bordure** - 0px (aucune), 1px (fine), 2px (standard), 3px (épaisse)
7. **Espacement** - Padding interne du bouton
   - Petit: 8px 20px
   - Standard: 12px 30px
   - Grand: 16px 40px
   - Très Grand: 20px 50px

### Typographie
8. **Taille Police** - De 14px à 20px
9. **Poids Police** - De 300 (léger) à 700 (gras)

## 📦 Installation

### Option 1: Nouvelle Installation
Si vous installez le système pour la première fois, utilisez :
```bash
php spark migrate
```

### Option 2: Installation Existante
Si vous avez déjà une table `theme_settings`, exécutez cette migration :
```bash
php spark migrate
```

Ou exécutez manuellement le fichier SQL :
```bash
mysql -u root -p rebencia_db < add_button_theme_columns.sql
```

## 🚀 Utilisation

### 1. Accéder à l'interface
```
http://localhost:8080/admin/theme
```

### 2. Section "Design des Boutons"
- Trouvez la nouvelle section avec l'icône 👆
- Modifiez les couleurs avec les sélecteurs de couleur
- Ajustez les tailles et espacements avec les menus déroulants
- Voyez l'aperçu en temps réel dans le panneau de droite

### 3. Enregistrer
Cliquez sur "Enregistrer les Modifications" pour appliquer les changements au site.

## 💡 Exemples de Styles

### Bouton Moderne (Défaut)
- Fond: #667eea
- Texte: #ffffff
- Survol: #764ba2
- Bordure: Aucune
- Padding: 12px 30px
- Police: 16px / 500

### Bouton Outline
- Fond: transparent (#ffffff)
- Texte: #667eea
- Bordure: 2px #667eea
- Survol Fond: #667eea
- Survol Texte: #ffffff

### Bouton Minimaliste
- Fond: #f7fafc
- Texte: #2d3748
- Bordure: 1px #e2e8f0
- Padding: 8px 20px
- Police: 14px / 400

### Bouton Call-to-Action
- Fond: #f5576c
- Texte: #ffffff
- Survol: #764ba2
- Padding: 20px 50px
- Police: 18px / 700

## 🔧 Variables CSS Générées

Les paramètres créent automatiquement ces variables CSS :
```css
:root {
    --button-bg-color: #667eea;
    --button-text-color: #ffffff;
    --button-hover-bg-color: #764ba2;
    --button-hover-text-color: #ffffff;
    --button-border-width: 0px;
    --button-border-color: #667eea;
    --button-padding: 12px 30px;
    --button-font-size: 16px;
    --button-font-weight: 500;
}
```

## 📋 Fichiers Modifiés

### Base de données
- `database_manual_setup.sql` - Structure complète avec colonnes boutons
- `add_button_theme_columns.sql` - Ajout colonnes aux tables existantes
- `2026-02-09-160000_AddButtonThemeColumns.php` - Migration CodeIgniter
- `2026-02-09-140100_CreateThemeSettingsTable.php` - Table initiale mise à jour

### Modèles
- `app/Models/ThemeSettingModel.php` - Ajout des 9 nouveaux champs dans allowedFields et getCurrentTheme()

### Vues
- `app/Views/admin/theme/index.php` - Section "Design des Boutons" avec 9 contrôles + aperçu

### Helpers
- `app/Helpers/theme_helper.php` - CSS par défaut mis à jour avec variables boutons

### CSS Généré
Le fichier `public/assets/css/theme.css` contiendra automatiquement les styles des boutons personnalisés.

## ✅ Test

1. Allez à `/admin/theme`
2. Changez la couleur fond bouton en rouge (#ff0000)
3. Changez le texte en blanc (#ffffff)
4. Réglez le padding à "Grand"
5. Enregistrez
6. Visitez la page d'accueil
7. Les boutons doivent être rouges avec le nouveau style

## 🎯 Éléments Concernés

Les styles s'appliquent à :
- `.btn`
- `.button`
- `button.btn-primary`
- `a.btn-primary`
- Tous les boutons avec la classe Bootstrap `.btn`

Les styles incluent une transition de 0.3s pour des effets fluides au survol.
