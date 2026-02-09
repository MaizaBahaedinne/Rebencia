# ⚠️ CHECKLIST DE VÉRIFICATION - SYSTÈME SLIDERS & THÈME

## 1️⃣ VÉRIFICATION DES FICHIERS

### Migrations
- ✅ `app/Database/Migrations/2026-02-09-140000_CreateSlidersTable.php`
- ✅ `app/Database/Migrations/2026-02-09-140100_CreateThemeSettingsTable.php`

### Modèles
- ✅ `app/Models/SliderModel.php` (button1_text, button1_link, button2_text, button2_link)
- ✅ `app/Models/ThemeSettingModel.php`

### Contrôleurs
- ✅ `app/Controllers/Admin/Sliders.php`
- ✅ `app/Controllers/Admin/Theme.php`

### Vues
- ✅ `app/Views/admin/sliders/index.php`
- ✅ `app/Views/admin/sliders/create.php`
- ✅ `app/Views/admin/sliders/edit.php`
- ✅ `app/Views/admin/theme/index.php`
- ✅ `app/Views/components/slider.php`

### Routes
- ✅ Routes sliders dans `app/Config/Routes.php` (lignes 292-301)
- ✅ Routes thème dans `app/Config/Routes.php` (lignes 303-309)

### Menu Admin
- ✅ Section "Site Web" avec sous-menu dans `app/Views/layouts/admin_modern.php`
- ✅ CSS pour les sous-menus accordéons
- ✅ JavaScript pour toggle des sous-menus

---

## 2️⃣ VÉRIFICATION BASE DE DONNÉES

### Option A : Avec migrations (si PHP fonctionne)
```bash
php spark migrate
```

### Option B : Manuellement (si PHP ne fonctionne pas)
```bash
# Se connecter à MySQL/MariaDB
mysql -u root -p rebencia_db

# Exécuter le script SQL
source /Users/mac/Documents/Rebencia/database_manual_setup.sql

# Vérifier les tables
SHOW TABLES LIKE 'sliders';
SHOW TABLES LIKE 'theme_settings';

# Vérifier la structure
DESC sliders;
DESC theme_settings;
```

### Vérifier que les colonnes sont correctes
```sql
-- Dans la table sliders, on doit avoir :
-- button1_text, button1_link, button2_text, button2_link
-- PAS button_text, button_link, button_text_2, button_link_2

SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'sliders' 
AND COLUMN_NAME LIKE 'button%';
```

---

## 3️⃣ VÉRIFICATION DES SOUS-MENUS

### CSS des sous-menus
Vérifier dans `app/Views/layouts/admin_modern.php` :
- ✅ `.menu-item.has-submenu` (lignes ~177-182)
- ✅ `.submenu` (lignes ~184-192)
- ✅ `.submenu-item` (lignes ~194-217)

### JavaScript des sous-menus
Vérifier dans `app/Views/layouts/admin_modern.php` :
- ✅ Toggle des sous-menus (lignes ~1281-1309)
- ✅ Sauvegarde de l'état dans localStorage
- ✅ Auto-ouverture des sous-menus actifs

### Test manuel des sous-menus
1. Aller dans l'admin : `/admin`
2. Cliquer sur "Gestion" → doit s'ouvrir/fermer
3. Cliquer sur "Site Web" → doit s'ouvrir/fermer
4. Recharger la page → les sous-menus ouverts doivent rester ouverts
5. Aller sur `/admin/sliders` → le sous-menu "Site Web" doit être ouvert automatiquement

---

## 4️⃣ VÉRIFICATION DES PERMISSIONS

### Dossiers uploads
```bash
# Créer et donner les permissions
mkdir -p /Users/mac/Documents/Rebencia/public/uploads/sliders
chmod -R 755 /Users/mac/Documents/Rebencia/public/uploads

# Vérifier
ls -la /Users/mac/Documents/Rebencia/public/uploads/
```

### Fichier CSS du thème
```bash
# Créer le dossier si nécessaire
mkdir -p /Users/mac/Documents/Rebencia/public/assets/css

# Vérifier que theme.css existe
ls -la /Users/mac/Documents/Rebencia/public/assets/css/theme.css
```

---

## 5️⃣ TEST FONCTIONNEL

### Test des sliders
1. Se connecter à l'admin
2. Aller dans "Site Web > Sliders"
3. Cliquer sur "Nouveau Slider"
4. Upload une image (max 2 Mo)
5. Remplir titre, sous-titre, description
6. Configurer les 2 boutons (texte + lien)
7. Choisir animation (fade/slide/zoom)
8. Choisir position du texte (gauche/centre/droite)
9. Régler l'opacité de l'overlay
10. Enregistrer
11. Vérifier que le slider apparaît dans la liste
12. Toggle le statut actif/inactif
13. Éditer le slider
14. Supprimer le slider

### Test du thème
1. Aller dans "Site Web > Thème"
2. Changer les couleurs avec les color pickers
3. Vérifier l'aperçu en temps réel
4. Changer les polices
5. Ajuster la taille de base
6. Modifier le rayon de bordure
7. Cliquer sur "Enregistrer les Modifications"
8. Aller sur le site public
9. Vérifier que les couleurs/polices sont appliquées
10. Tester "Réinitialiser" pour revenir aux valeurs par défaut

---

## 6️⃣ PROBLÈMES COURANTS

### 🚨 Erreur : "Table 'sliders' doesn't exist"
**Solution :** Exécuter le fichier SQL manuellement :
```bash
mysql -u root -p rebencia_db < /Users/mac/Documents/Rebencia/database_manual_setup.sql
```

### 🚨 Erreur : "Unknown column 'button_text'"
**Solution :** Les noms ont été corrigés dans la migration. Supprimer la table et recréer :
```sql
DROP TABLE IF EXISTS sliders;
```
Puis relancer la migration ou le script SQL.

### 🚨 Les sous-menus ne s'ouvrent pas
**Solution :** 
1. Vérifier que le JavaScript est chargé (voir console navigateur F12)
2. Vérifier que les classes CSS existent (`.has-submenu`, `.submenu`)
3. Vider le cache du navigateur (Cmd+Shift+R)

### 🚨 Erreur : "Failed to upload image"
**Solution :** Vérifier les permissions du dossier uploads :
```bash
chmod -R 755 /Users/mac/Documents/Rebencia/public/uploads
chown -R www-data:www-data /Users/mac/Documents/Rebencia/public/uploads
```

### 🚨 Le thème ne s'applique pas
**Solution :**
1. Vérifier que `theme.css` est chargé dans le layout public
2. Aller dans `/admin/theme` et cliquer sur "Enregistrer" pour régénérer le CSS
3. Vider le cache du navigateur

---

## 7️⃣ COMMANDES UTILES

### Vérifier PHP
```bash
which php
php -v
/opt/homebrew/bin/php -v
```

### Vérifier la base de données
```bash
mysql -u root -p
SHOW DATABASES;
USE rebencia_db;
SHOW TABLES;
```

### Nettoyer le cache CodeIgniter
```bash
rm -rf /Users/mac/Documents/Rebencia/writable/cache/*
rm -rf /Users/mac/Documents/Rebencia/writable/session/*
```

### Voir les logs
```bash
tail -f /Users/mac/Documents/Rebencia/writable/logs/log-*.log
```

---

## ✅ VALIDATION FINALE

- [ ] Les tables `sliders` et `theme_settings` existent dans la base
- [ ] Le menu admin affiche la section "Site Web" avec 4 sous-éléments
- [ ] Les sous-menus s'ouvrent/ferment au clic
- [ ] On peut créer/éditer/supprimer des sliders
- [ ] Les images s'uploadent correctement
- [ ] On peut personnaliser le thème (couleurs, polices)
- [ ] L'aperçu en temps réel fonctionne
- [ ] Le thème s'applique sur le site public
- [ ] Le fichier `database_manual_setup.sql` est prêt si besoin

---

**Date de création :** 9 février 2026  
**Dernière mise à jour :** 9 février 2026
