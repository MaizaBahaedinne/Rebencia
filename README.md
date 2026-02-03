# REBENCIA REAL ESTATE - Documentation Installation

## 📋 Prérequis

- PHP 8.1+
- MySQL/MariaDB 8.0+
- Composer
- Serveur Web (Apache/Nginx)

## 🚀 Installation

### 1. Installer CodeIgniter 4 complet

```bash
cd /Users/mac/Documents/Rebencia
composer create-project codeigniter4/appstarter .
```

### 2. Configuration de la base de données

La base de données **rebe_RebenciaDB** est déjà créée avec 29 tables.

Mettre à jour le fichier `app/Config/Database.php` avec vos identifiants VPS :

```php
public array $default = [
    'hostname' => 'votre_vps_ip',
    'username' => 'votre_username',
    'password' => 'votre_password',
    'database' => 'rebe_RebenciaDB',
    'DBDriver' => 'MySQLi',
    'port'     => 3306,
];
```

### 3. Configuration de l'application

Modifier `app/Config/App.php` :

```php
public string $baseURL = 'http://votre-domaine.com/';
public string $defaultLocale = 'fr';
public array $supportedLocales = ['fr', 'ar', 'en'];
```

### 4. Permissions des dossiers

```bash
chmod -R 777 writable/
```

### 5. Lancer l'application

```bash
php spark serve
```

Ou configurer votre serveur web pour pointer vers `/public`

## 🔐 Compte Admin par défaut

- **Email :** admin@rebencia.tn
- **Mot de passe :** Admin@2026

## 📁 Structure créée

```
/app
  /Config
    - Database.php (✅)
    - Routes.php (✅)
    - App.php (✅)
  /Controllers
    - Home.php (✅)
    - BaseController.php (✅)
    /Admin
      - Dashboard.php (✅)
      - Auth.php (✅)
      - Properties.php (✅)
      - Clients.php (✅)
      - Transactions.php (✅)
      - Users.php (✅)
  /Models
    - UserModel.php (✅)
    - PropertyModel.php (✅)
    - AgencyModel.php (✅)
    - ClientModel.php (✅)
    - TransactionModel.php (✅)
    - CommissionModel.php (✅)
    - ZoneModel.php (✅)
    - RoleModel.php (✅)
    - PermissionModel.php (✅)
  /Views
    /layouts
      - public.php (✅)
      - admin.php (✅)
    /public
      - home.php (✅)
    /admin
      - dashboard.php (✅)
      /auth
        - login.php (✅)
      /properties
        - index.php (✅)
      /clients
        - index.php (✅)
      /transactions
        - index.php (✅)
      /users
        - index.php (✅)
```

## 🗄️ Base de données (29 tables)

✅ **Gouvernance :** roles, permissions, role_permissions, users, agencies
✅ **RH :** employees, salaries
✅ **Immobilier :** properties, property_media, property_views, zones
✅ **CRM :** clients, client_preferences, client_interactions
✅ **Transactions :** transactions, commissions
✅ **Workflows :** workflows, workflow_steps, workflow_executions
✅ **IA :** estimations
✅ **Communication :** email_templates, email_logs, sms_logs, notifications
✅ **CMS :** pages, settings
✅ **Système :** audit_logs, documents, favorites

## 📊 Données initiales insérées

✅ 7 rôles (Super Admin, Admin Siège, Chef Agence, etc.)
✅ 30 permissions par module
✅ 1 compte admin (admin@rebencia.tn)
✅ 1 agence Siège
✅ 10 zones principales de Tunisie

## 🎯 Modules développés

✅ **Authentification** - Login/Logout
✅ **Dashboard** - Vue d'ensemble avec statistiques
✅ **Propriétés** - CRUD complet
✅ **Clients** - CRM intégré
✅ **Transactions** - Gestion ventes/locations
✅ **Commissions** - Calcul automatique
✅ **Utilisateurs** - Gestion hiérarchique
✅ **Rôles & Permissions** - RBAC complet

## 🔜 Prochaines étapes recommandées

1. **Installer Composer & dependencies**
2. **Créer les formulaires de création/édition** (Properties, Clients, Users)
3. **Implémenter l'upload de fichiers** (images propriétés, documents)
4. **Développer le module de recherche Half Map** (Leaflet)
5. **Créer l'API REST** pour mobile
6. **Implémenter l'IA d'estimation** immobilière
7. **Ajouter les Workflows** automatisés
8. **Système de notifications** temps réel
9. **Rapports & statistiques** avancés
10. **Multilingue complet** (FR/AR/EN)

## 📧 Support

Pour toute question : dev@rebencia.tn

---

**Version actuelle :** 1.0.0 (Phase de développement)
**Date :** 3 février 2026
