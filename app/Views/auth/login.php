<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title ?? 'Connexion') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --rb-primary: #1a3c5e; --rb-accent: #e8a020; }
        body {
            background: linear-gradient(135deg, var(--rb-primary) 0%, #2d6a9f 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .login-card {
            background: #fff; border-radius: 1.5rem;
            padding: 2.5rem 2rem; width: 100%; max-width: 420px;
            box-shadow: 0 1.5rem 3rem rgba(0,0,0,.25);
        }
        .login-logo {
            font-size: 2rem; font-weight: 800; color: var(--rb-primary);
            letter-spacing: -.5px;
        }
        .login-logo span { color: var(--rb-accent); }
        .btn-login {
            background: var(--rb-primary); border: none; color: #fff;
            padding: .75rem; font-weight: 600;
            transition: background .2s;
        }
        .btn-login:hover { background: #2d6a9f; color: #fff; }
        .form-control:focus { border-color: var(--rb-primary); box-shadow: 0 0 0 .2rem rgba(26,60,94,.15); }
        .input-group-text { background: #f8f9fa; border-right: none; }
        .form-control { border-left: none; }
        .form-control:not(:focus) { border-left: 1px solid #ced4da; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="text-center mb-4">
        <div class="login-logo mb-1">Re<span>bencia</span></div>
        <p class="text-muted mb-0" style="font-size:.875rem;">Plateforme Immobilière</p>
    </div>

    <?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger py-2">
        <i class="bi bi-exclamation-triangle-fill me-1"></i>
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success py-2">
        <i class="bi bi-check-circle-fill me-1"></i>
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')) : ?>
    <div class="alert alert-danger py-2">
        <?php foreach ((array) session()->getFlashdata('errors') as $err) : ?>
        <div><i class="bi bi-dot"></i><?= esc($err) ?></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form action="<?= base_url('login') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label fw-semibold text-secondary" style="font-size:.875rem;">Adresse email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email" name="email" class="form-control"
                       value="<?= esc(old('email')) ?>"
                       placeholder="vous@agence.com" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold text-secondary" style="font-size:.875rem;">Mot de passe</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" name="password" id="pwd" class="form-control"
                       placeholder="••••••••" required>
                <button type="button" class="btn btn-outline-secondary border-start-0"
                        onclick="togglePwd()" title="Afficher/Masquer">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-login w-100 rounded-3 mb-3">
            <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
        </button>
    </form>

    <p class="text-center text-muted mb-0" style="font-size:.75rem;">
        &copy; <?= date('Y') ?> Rebencia — Tous droits réservés
    </p>
</div>

<script>
function togglePwd() {
    const pwd  = document.getElementById('pwd');
    const icon = document.getElementById('eyeIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        pwd.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
