# 📘 REBENCIA - Documentation Projet

## 🎯 Vue d'ensemble

**REBENCIA REAL ESTATE** - Plateforme immobilière multi-agences pour la Tunisie

- **Domaine:** https://rebencia.com
- **Framework:** CodeIgniter 4.x
- **PHP:** 8.3
- **Base de données:** MySQL/MariaDB (rebe_RebenciaDB)
- **Frontend:** Bootstrap 5, Leaflet/OpenStreetMap
- **Langues:** FR/AR/EN

---

## 📂 Architecture du Projet

```
Rebencia/
├── app/
│   ├── Config/
│   │   ├── App.php                 # Configuration principale
│   │   ├── Database.php            # Connexion DB
│   │   ├── Routes.php              # Définition des routes
│   │   └── Constants.php           # Constantes globales
│   │
│   ├── Controllers/
│   │   ├── Home.php                # Contrôleur page d'accueil
│   │   ├── BaseController.php      # Contrôleur de base
│   │   └── Admin/
│   │       ├── Dashboard.php       # Tableau de bord admin
│   │       ├── Auth.php            # Authentification
│   │       ├── Properties.php      # Gestion des biens
│   │       ├── Clients.php         # Gestion des clients
│   │       ├── Transactions.php    # Gestion des transactions
│   │       └── Users.php           # Gestion des utilisateurs
│   │
│   ├── Models/
│   │   ├── UserModel.php           # Utilisateurs
│   │   ├── PropertyModel.php       # Biens immobiliers
│   │   ├── AgencyModel.php         # Agences
│   │   ├── ClientModel.php         # Clients
│   │   ├── TransactionModel.php    # Transactions
│   │   ├── CommissionModel.php     # Commissions
│   │   ├── ZoneModel.php           # Zones géographiques
│   │   ├── RoleModel.php           # Rôles utilisateurs
│   │   └── PermissionModel.php     # Permissions
│   │
│   └── Views/
│       ├── layouts/
│       │   ├── public.php          # Layout site public
│       │   └── admin.php           # Layout panneau admin
│       ├── public/
│       │   └── home.php            # Page d'accueil
│       └── admin/
│           ├── dashboard.php       # Dashboard admin
│           ├── auth/
│           │   └── login.php       # Page de connexion
│           ├── properties/
│           │   └── index.php       # Liste des biens
│           ├── clients/
│           │   └── index.php       # Liste des clients
│           ├── transactions/
│           │   └── index.php       # Liste des transactions
│           └── users/
│               └── index.php       # Liste des utilisateurs
│
├── public/
│   ├── index.php                   # Point d'entrée
│   └── .htaccess                   # Configuration Apache
│
├── writable/
│   ├── cache/                      # Cache
│   ├── logs/                       # Logs
│   ├── session/                    # Sessions
│   └── uploads/                    # Fichiers uploadés
│
├── .env                            # Configuration environnement
├── composer.json                   # Dépendances PHP
└── README.md                       # Instructions projet
```

---

## 🗄️ Base de Données

### Tables Principales (29 tables)

#### 1️⃣ **Gestion des Utilisateurs**
- `roles` - Rôles (Super Admin, Directeur, Manager, Agent...)
- `permissions` - Permissions système
- `role_permissions` - Attribution permissions aux rôles
- `users` - Utilisateurs de la plateforme

#### 2️⃣ **Structure Organisationnelle**
- `agencies` - Agences immobilières
- `employees` - Employés des agences
- `salaries` - Historique des salaires

#### 3️⃣ **Gestion Immobilière**
- `zones` - Zones géographiques (Tunis, Sfax, Sousse...)
- `properties` - Biens immobiliers
- `property_media` - Photos/vidéos des biens
- `property_views` - Historique des vues

#### 4️⃣ **Gestion Clients**
- `clients` - Clients/prospects
- `client_preferences` - Préférences clients
- `client_interactions` - Historique interactions
- `favorites` - Biens favoris

#### 5️⃣ **Transactions**
- `transactions` - Ventes/locations
- `commissions` - Commissions calculées

#### 6️⃣ **Workflows & Automation**
- `workflows` - Définition des workflows
- `workflow_steps` - Étapes des workflows
- `workflow_executions` - Exécutions en cours

#### 7️⃣ **Estimation IA**
- `estimations` - Estimations de prix par IA

#### 8️⃣ **Communications**
- `email_templates` - Templates d'emails
- `email_logs` - Logs emails envoyés
- `sms_logs` - Logs SMS envoyés
- `notifications` - Notifications système

#### 9️⃣ **CMS & Configuration**
- `pages` - Pages CMS
- `settings` - Paramètres globaux
- `documents` - Documents/contrats
- `audit_logs` - Logs d'audit

### Credentials Base de Données

**Production (VPS):**
```env
DB_HOST=localhost
DB_NAME=rebe_RebenciaDB
DB_USER=rebe_RebenciaDB
DB_PASS=RebenciaDB2026!!
```

**Local:**
```env
DB_HOST=localhost
DB_NAME=rebe_RebenciaDB
DB_USER=root
DB_PASS=RebenciaDB2026!!
```

---

## 👥 Système RBAC (Rôles & Permissions)

### Hiérarchie des Rôles

| Niveau | Rôle | Description |
|--------|------|-------------|
| 100 | Super Admin | Administrateur global du système |
| 90 | Directeur Siège | Directeur du siège social |
| 80 | Manager Siège | Manager du siège social |
| 70 | Directeur Agence | Directeur d'agence |
| 60 | Manager Agence | Manager d'agence |
| 50 | Agent Immobilier | Agent immobilier |
| 40 | Assistant | Assistant administratif |

### 30 Permissions Définies

**Dashboard:** view_dashboard

**Utilisateurs:** manage_users, manage_roles, manage_permissions

**Biens:** view_properties, create_properties, edit_properties, delete_properties

**Clients:** view_clients, create_clients, edit_clients, delete_clients

**Transactions:** view_transactions, create_transactions, edit_transactions, delete_transactions

**Commissions:** view_commissions, manage_commissions

**Agences:** view_agencies, manage_agencies

**Rapports:** view_reports, export_data

**Système:** manage_settings, manage_workflows, manage_zones, view_audit_logs, manage_templates, send_notifications

**Estimations IA:** view_estimations, create_estimations

---

## 🔐 Authentification

### Compte Admin par Défaut

```
Email: admin@rebencia.tn
Mot de passe: password (à changer après connexion)
```

### URLs d'Accès

- **Site public:** https://rebencia.com
- **Panneau admin:** https://rebencia.com/admin/login
- **Dashboard:** https://rebencia.com/admin/dashboard

---

## 🚀 Déploiement

### Configuration Serveur

**Hébergement:** VPS avec CyberPanel
**PHP Version:** 8.3 (compatible)
**Web Server:** Apache/Nginx
**SSL:** Let's Encrypt (à installer)
**Path:** /home/rebencia.com/public_html

### Variables d'Environnement (.env)

```env
CI_ENVIRONMENT = production
app.baseURL = 'https://rebencia.com/'

database.default.hostname = localhost
database.default.database = rebe_RebenciaDB
database.default.username = rebe_RebenciaDB
database.default.password = RebenciaDB2026!!
database.default.DBDriver = MySQLi
database.default.port = 3306
```

### Déploiement via Git

1. **Commit local:**
```bash
git add .
git commit -m "Description des modifications"
git push origin main
```

2. **Pull sur serveur:**
```bash
cd /home/rebencia.com/public_html
git pull origin main
```

3. **Permissions:**
```bash
chmod -R 755 writable/
chmod -R 644 .env
```

---

## 📊 État d'Avancement

### ✅ Phase 1 - Infrastructure (TERMINÉE)

- [x] Base de données (29 tables)
- [x] Configuration CodeIgniter 4
- [x] 9 Models avec relations
- [x] 6 Controllers Admin
- [x] 9 Views (layouts + pages)
- [x] Système d'authentification
- [x] Déploiement production
- [x] Panneau admin accessible

### 🔄 Phase 2 - CRUD & Formulaires (EN COURS)

- [ ] Formulaire création bien immobilier
- [ ] Formulaire édition bien immobilier
- [ ] Upload d'images multiples
- [ ] Formulaire création client
- [ ] Formulaire création transaction
- [ ] Formulaire création utilisateur
- [ ] Validation des données
- [ ] Messages flash (success/error)
- [ ] Pagination des listes

### 📋 Phase 3 - Fonctionnalités Avancées

- [ ] Recherche Half Map (carte + liste)
- [ ] Filtres avancés (prix, type, zone...)
- [ ] Estimation IA des biens
- [ ] Workflows automatisés
- [ ] Calcul automatique des commissions
- [ ] Notifications temps réel
- [ ] Gestion des documents/contrats

### 🌐 Phase 4 - Site Public

- [ ] Page d'accueil responsive
- [ ] Catalogue des biens
- [ ] Détails des propriétés
- [ ] Formulaire de contact
- [ ] Recherche avancée
- [ ] Système de favoris
- [ ] Multilingue FR/AR/EN

### 🔌 Phase 5 - API & Intégrations

- [ ] API REST complète
- [ ] Documentation API (Swagger)
- [ ] Intégration Email (SMTP)
- [ ] Intégration SMS
- [ ] Templates personnalisables
- [ ] Rapports PDF
- [ ] Export Excel/CSV

---

## 🛠️ Commandes Utiles

### Git
```bash
# Status
git status

# Commit
git add .
git commit -m "message"
git push

# Pull
git pull origin main

# Branches
git branch
git checkout -b nouvelle-branche
```

### Composer
```bash
# Installer dépendances
composer install

# Mettre à jour
composer update

# Autoload
composer dump-autoload
```

### Base de Données
```bash
# Export
mysqldump -u user -p database > backup.sql

# Import
mysql -u user -p database < backup.sql

# Connexion
mysql -u rebe_RebenciaDB -p rebe_RebenciaDB
```

---

## 📞 Support & Maintenance

### Logs
- **Erreurs PHP:** `/writable/logs/log-YYYY-MM-DD.php`
- **Erreurs Serveur:** `/var/log/apache2/error.log` ou `/var/log/nginx/error.log`

### Debug
```env
# Activer le mode debug dans .env
CI_ENVIRONMENT = development
```

### Cache
```bash
# Vider le cache
rm -rf writable/cache/*
```

---

## 📝 Notes de Développement

### Conventions de Code

- **Controllers:** PascalCase (PropertiesController)
- **Models:** PascalCase + Model suffix (PropertyModel)
- **Views:** snake_case (properties/create.php)
- **Methods:** camelCase (createProperty)
- **Variables:** camelCase ($propertyData)
- **Constants:** UPPER_SNAKE_CASE (MAX_UPLOAD_SIZE)

### Structure des Routes

```php
// Admin routes
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('properties', 'Properties::index');
    $routes->get('properties/create', 'Properties::create');
    $routes->post('properties/store', 'Properties::store');
});
```

---

## 🔄 Changelog

### Version 1.0.0 (3 février 2026)
- ✅ Infrastructure de base
- ✅ Base de données complète
- ✅ Authentification fonctionnelle
- ✅ Déploiement production
- 🔄 Phase 2 en cours

---

**Dernière mise à jour:** 3 février 2026
**Version:** 1.0.0
**Statut:** En développement actif
