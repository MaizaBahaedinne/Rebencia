<?php
/**
 * REBENCIA — Migration : Module Agences
 * Accès : https://rebencia.com/migrate_agencies.php
 * SUPPRIMER ce fichier immédiatement après l'exécution !
 */

// ── Sécurité basique : token URL ───────────────────────────────────────────
define('MIGRATION_TOKEN', 'reb2026agencies');
if (($_GET['token'] ?? '') !== MIGRATION_TOKEN) {
    http_response_code(403);
    die('<h2>403 Forbidden</h2><p>Accès refusé. Ajoutez <code>?token=reb2026agencies</code> à l\'URL.</p>');
}

// ── Charger la config CI4 ──────────────────────────────────────────────────
define('FCPATH', __DIR__ . '/');
$envFile = __DIR__ . '/.env';
$dbHost = 'localhost'; $dbUser = 'root'; $dbPass = ''; $dbName = 'rebencia';

if (file_exists($envFile)) {
    foreach (file($envFile) as $line) {
        $line = trim($line);
        if (str_starts_with($line, 'database.default.hostname')) $dbHost = explode('=', $line, 2)[1] ?? $dbHost;
        if (str_starts_with($line, 'database.default.username')) $dbUser = explode('=', $line, 2)[1] ?? $dbUser;
        if (str_starts_with($line, 'database.default.password')) $dbPass = explode('=', $line, 2)[1] ?? $dbPass;
        if (str_starts_with($line, 'database.default.database'))  $dbName = explode('=', $line, 2)[1] ?? $dbName;
    }
}

// Nettoyage des valeurs lues
$dbHost = trim(str_replace(['"',"'"], '', $dbHost));
$dbUser = trim(str_replace(['"',"'"], '', $dbUser));
$dbPass = trim(str_replace(['"',"'"], '', $dbPass));
$dbName = trim(str_replace(['"',"'"], '', $dbName));

$pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$steps = [];

function run(PDO $pdo, string $label, string $sql): void {
    global $steps;
    try {
        $pdo->exec($sql);
        $steps[] = ['ok', $label];
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        // Ignorer les erreurs "déjà existant"
        if (str_contains($msg, 'already exists') || str_contains($msg, 'Duplicate') || str_contains($msg, 'duplicate')) {
            $steps[] = ['skip', $label . ' (déjà présent)'];
        } else {
            $steps[] = ['err', $label . ' — ' . $msg];
        }
    }
}

$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

// 1. Table agencies
run($pdo, 'CREATE TABLE agencies', "
CREATE TABLE IF NOT EXISTS `agencies` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(150)     NOT NULL,
  `slug`        VARCHAR(160)     NOT NULL UNIQUE,
  `email`       VARCHAR(191),
  `phone`       VARCHAR(30),
  `address`     VARCHAR(255),
  `city`        VARCHAR(100),
  `logo`        VARCHAR(255),
  `description` TEXT,
  `zone_id`     INT UNSIGNED     NULL,
  `is_active`   TINYINT(1)       NOT NULL DEFAULT 1,
  `created_at`  DATETIME,
  `updated_at`  DATETIME,
  `deleted_at`  DATETIME,
  PRIMARY KEY (`id`),
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

// 2. users.agency_id
run($pdo, 'ALTER users ADD agency_id', "ALTER TABLE `users` ADD COLUMN `agency_id` INT UNSIGNED NULL AFTER `role_id`");
run($pdo, 'INDEX users.agency_id',     "ALTER TABLE `users` ADD KEY `idx_users_agency` (`agency_id`)");
run($pdo, 'FK users → agencies',       "ALTER TABLE `users` ADD CONSTRAINT `fk_users_agency` FOREIGN KEY (`agency_id`) REFERENCES `agencies` (`id`) ON DELETE SET NULL");

// 3. properties.agency_id
run($pdo, 'ALTER properties ADD agency_id', "ALTER TABLE `properties` ADD COLUMN `agency_id` INT UNSIGNED NULL AFTER `agent_id`");
run($pdo, 'INDEX properties.agency_id',     "ALTER TABLE `properties` ADD KEY `idx_properties_agency` (`agency_id`)");
run($pdo, 'FK properties → agencies',       "ALTER TABLE `properties` ADD CONSTRAINT `fk_properties_agency` FOREIGN KEY (`agency_id`) REFERENCES `agencies` (`id`) ON DELETE SET NULL");

// 4. Permissions
run($pdo, "PERMISSION agencies.view",   "INSERT IGNORE INTO `permissions` (`name`,`label`,`module`,`created_at`) VALUES ('agencies.view','Voir les agences','agencies',NOW())");
run($pdo, "PERMISSION agencies.create", "INSERT IGNORE INTO `permissions` (`name`,`label`,`module`,`created_at`) VALUES ('agencies.create','Créer des agences','agencies',NOW())");
run($pdo, "PERMISSION agencies.edit",   "INSERT IGNORE INTO `permissions` (`name`,`label`,`module`,`created_at`) VALUES ('agencies.edit','Modifier des agences','agencies',NOW())");
run($pdo, "PERMISSION agencies.delete", "INSERT IGNORE INTO `permissions` (`name`,`label`,`module`,`created_at`) VALUES ('agencies.delete','Supprimer des agences','agencies',NOW())");

// 5. Assigner permissions aux rôles
run($pdo, 'ROLE PERMS super_admin+admin (agencies.*)', "
INSERT IGNORE INTO `role_permissions` (`role_id`,`permission_id`,`created_at`)
SELECT r.id, p.id, NOW() FROM `permissions` p CROSS JOIN `roles` r
WHERE r.name IN ('super_admin','admin') AND p.name LIKE 'agencies.%'
");
run($pdo, 'ROLE PERMS director (agencies.view)', "
INSERT IGNORE INTO `role_permissions` (`role_id`,`permission_id`,`created_at`)
SELECT r.id, p.id, NOW() FROM `permissions` p CROSS JOIN `roles` r
WHERE r.name = 'director' AND p.name = 'agencies.view'
");

// 6. Agences démo
run($pdo, 'AGENCY démo Tunis',  "INSERT IGNORE INTO `agencies` (`name`,`slug`,`email`,`city`,`is_active`,`created_at`,`updated_at`) VALUES ('Agence Principale Tunis','agence-principale-tunis','contact@rebencia.com','Tunis',1,NOW(),NOW())");
run($pdo, 'AGENCY démo Sfax',   "INSERT IGNORE INTO `agencies` (`name`,`slug`,`email`,`city`,`is_active`,`created_at`,`updated_at`) VALUES ('Agence Sfax','agence-sfax','sfax@rebencia.com','Sfax',1,NOW(),NOW())");
run($pdo, 'AGENCY démo Sousse', "INSERT IGNORE INTO `agencies` (`name`,`slug`,`email`,`city`,`is_active`,`created_at`,`updated_at`) VALUES ('Agence Sousse','agence-sousse','sousse@rebencia.com','Sousse',1,NOW(),NOW())");

$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

// ── Rendu HTML ─────────────────────────────────────────────────────────────
$hasErr = array_filter($steps, fn($s) => $s[0] === 'err');
?><!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8">
<title>Migration Agences — Rebencia</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head><body class="bg-light">
<div class="container py-5" style="max-width:700px">
  <h3 class="fw-bold mb-1"><i class="bi bi-buildings"></i> Migration : Module Agences</h3>
  <p class="text-muted mb-4">Base : <strong><?= htmlspecialchars($dbName) ?></strong> sur <strong><?= htmlspecialchars($dbHost) ?></strong></p>

  <?php foreach ($steps as [$status, $label]): ?>
  <?php $cls = $status === 'ok' ? 'success' : ($status === 'skip' ? 'secondary' : 'danger'); ?>
  <div class="d-flex align-items-center gap-2 mb-1">
    <span class="badge text-bg-<?= $cls ?>" style="width:60px;text-align:center;">
      <?= $status === 'ok' ? 'OK' : ($status === 'skip' ? 'SKIP' : 'ERREUR') ?>
    </span>
    <span style="font-size:.9rem;"><?= htmlspecialchars($label) ?></span>
  </div>
  <?php endforeach; ?>

  <hr>
  <?php if ($hasErr): ?>
  <div class="alert alert-danger">Des erreurs ont eu lieu. Vérifiez les détails ci-dessus.</div>
  <?php else: ?>
  <div class="alert alert-success fw-semibold">✅ Migration appliquée avec succès.</div>
  <?php endif; ?>

  <div class="alert alert-warning mt-3">
    ⚠️ <strong>Supprimez ce fichier immédiatement</strong> depuis votre serveur après exécution :<br>
    <code>/migrate_agencies.php</code>
  </div>
  <a href="/admin/agencies" class="btn btn-primary">Aller aux Agences →</a>
</div>
</body></html>
