# Rebencia — Instructions GitHub Copilot

Plateforme immobilière multi-rôles construite avec **CodeIgniter 4** (PHP 8.1+) et **MySQL**.  
Ces instructions s'appliquent à tout le projet.

---

## Stack technique

| Composant | Détails |
|-----------|---------|
| Framework | CodeIgniter 4 (namespace `App\`) |
| PHP | 8.1+ — utiliser types stricts, null safe operator, match() |
| Base de données | MySQL 8 — moteur InnoDB, charset utf8mb4_unicode_ci |
| Frontend | Bootstrap 5.3.3 + Bootstrap Icons 1.11.3 (CDN uniquement) |
| Auth | Session CI4 — `AuthLibrary` + filtre `auth` |
| CLI | `php spark` (jamais `artisan`) |

---

## Structure du projet

```
app/
  Controllers/
    Auth/          → LoginController
    Admin/         → DashboardController, UsersController, PropertiesController,
                       LeadsController, RolesController, SystemController
  Models/          → UserModel, PropertyModel, LeadModel, RoleModel,
                     PermissionModel, ActivityLogModel, SystemLogModel, DeploymentModel
  Libraries/       → AuthLibrary, LogLibrary
  Filters/         → AuthFilter (protège /admin/*)
  Views/
    layouts/       → main.php (layout admin Bootstrap)
    auth/          → login.php
    admin/         → dashboards/, properties/, leads/, users/, roles/, system/
    errors/html/   → error_404.php, error_500.php, production.php
  Config/
    Routes.php     → auto-routing DÉSACTIVÉ, tout est explicite
    Filters.php    → 'auth' => AuthFilter::class
rebencia_schema.sql  → schéma complet v1.2 (source de vérité)
fix_roles_table.sql  → migration pour BDD existantes en production
```

---

## Base de données — 13 tables

```
roles · permissions · role_permissions
users · properties · property_images · property_history
leads · lead_status_history · lead_notes
activity_logs · system_logs · deployments
```

### Colonnes critiques — ne jamais se tromper

| Table | Points d'attention |
|-------|--------------------|
| `roles` | Colonnes : `name`, **`label`**, `color`, `is_active` — toujours utiliser `COALESCE(r.label, r.name) AS role_label` dans les JOIN |
| `users` | `password_hash` (pas `password`) — `deleted_at` existe |
| `properties` | `type` (pas `property_type`), `surface` (pas `surface_area`), `agent_id`, `deleted_at` |
| `property_history` | `field_changed` (pas `field_name`), `action VARCHAR(50)` |
| `leads` | Status ENUM = `'new','contacted','interested','visit_done','negotiating','won','lost'` — `desired_surface`, `desired_location` présents — `deleted_at` existe |
| `lead_notes` | Colonne `note` (pas `content`) |
| `deployments` | `note VARCHAR(255)` présent |

---

## Authentification & Permissions

- Session stockée avec la clé `auth_user` (array complet utilisateur + `permissions`)
- `AuthLibrary::attempt($email, $password)` → `true` ou message d'erreur string
- `AuthLibrary::check()` → bool — `AuthLibrary::user()` → array|null
- `AuthLibrary::hasPermission('module.action')` → bool
- Permissions disponibles : `users.*`, `roles.*`, `properties.*`, `leads.*`, `stats.*`, `system.*`
- Compte admin défaut : `admin@rebencia.com` / `Admin@2024` (hash bcrypt cost 12)

---

## Conventions de code

### Controllers Admin
- Héritent de `App\Controllers\BaseController`
- Injecter les libs dans le constructeur : `$this->auth = new AuthLibrary();`
- Vérifier les permissions avant chaque action : `$this->auth->hasPermission('module.action')`
- Retourner les vues via `return view('admin/module/page', $data)`
- Redirection après POST : `return redirect()->to(base_url('admin/route'))->with('success', 'msg')`

### Models
- Étendent `CodeIgniter\Model`
- `$table`, `$primaryKey`, `$useTimestamps = true`, `$useSoftDeletes = true`
- `$allowedFields` doit lister explicitement tous les champs modifiables
- Requêtes complexes avec `$this->db->query()` ou query builder — jamais de raw string non préparée

### Vues
- Toujours utiliser le layout : `<?= $this->extend('layouts/main') ?>`
- Sections : `<?= $this->section('content') ?>` ... `<?= $this->endSection() ?>`
- Données HTML échappées : `<?= esc($var) ?>` (jamais `echo $var` directement)
- Flash messages disponibles : `success`, `error`, `warning`, `info`

### Routes
- Auto-routing **désactivé** — toute nouvelle route doit être ajoutée dans `app/Config/Routes.php`
- Toutes les routes admin sont dans le groupe `'admin'` avec `['filter' => 'auth']`
- Pattern : `GET /admin/module` → index, `GET /admin/module/create` → form, `POST /admin/module/store` → save

---

## Logging

Utiliser `LogLibrary` pour les logs, jamais `log_message()` directement :

```php
$logLib = new \App\Libraries\LogLibrary();
$logLib->activity('action', 'module', 'entity_type', $entityId, 'Description');
$logLib->system('error', 'channel', 'Message', ['context' => 'data']);
```

---

## Sécurité (OWASP)

- Toutes les requêtes SQL utilisent des requêtes préparées (query builder CI4)
- Validation côté serveur obligatoire sur tous les formulaires via `$this->validate()`
- CSRF activé globalement dans CI4
- Mots de passe uniquement avec `password_hash()` cost 12 / `password_verify()`
- Ne jamais exposer `password_hash` dans les vues ou API
- Permissions vérifiées dans chaque méthode de controller (pas seulement dans le filtre)

---

## Commandes utiles

```bash
# Lancer le serveur local
php spark serve

# Créer une migration
php spark make:migration NomMigration

# Lancer les migrations
php spark migrate

# Vérifier les erreurs PHP
C:\xampp1\php\php.exe -l app/Controllers/Admin/MonController.php

# Import schema complet (fresh)
mysql -u root rebencia < rebencia_schema.sql

# Migration production (BDD existante)
mysql -u root rebencia < fix_roles_table.sql
```

---

## Environnement

- **Local** : XAMPP — `http://localhost/Rebencia/public/`
- **Production** : `https://rebencia.com/` — DB name `rebe_RebenciaDB`
- Config via `.env` (ne jamais committer le `.env` de production)
- `writable/` ignoré par git (logs, cache, sessions)
