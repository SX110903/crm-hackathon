<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acceso — <?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <style>
    /* ── Layout de pantalla completa para login ─────────────────────────── */
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      background: var(--color-bg);
    }

    .login-wrapper {
      width: 100%;
      max-width: 420px;
      padding: 1.5rem;
    }

    .login-logo {
      text-align: center;
      margin-bottom: 2rem;
    }

    .login-logo h1 {
      font-size: 1.4rem;
      font-weight: 700;
      color: var(--color-text);
      margin-bottom: .25rem;
    }

    .login-logo p {
      font-size: .85rem;
      color: var(--color-text-muted);
    }

    .login-card {
      background: var(--color-surface);
      border-radius: var(--radius);
      box-shadow: var(--shadow-md);
      padding: 2rem;
    }

    .login-card h2 {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
      color: var(--color-text);
    }

    .form-group {
      margin-bottom: 1.25rem;
    }

    .form-group label {
      display: block;
      font-size: .82rem;
      font-weight: 600;
      color: var(--color-text);
      margin-bottom: .4rem;
    }

    .form-group input {
      width: 100%;
      padding: .6rem .75rem;
      border: 1px solid var(--color-border);
      border-radius: var(--radius-sm);
      font-size: .9rem;
      color: var(--color-text);
      background: var(--color-surface);
      transition: border-color var(--transition), box-shadow var(--transition);
      outline: none;
    }

    .form-group input:focus {
      border-color: var(--color-primary);
      box-shadow: 0 0 0 3px rgba(37,99,235,.15);
    }

    .btn-login {
      width: 100%;
      padding: .65rem;
      background: var(--color-primary);
      color: #fff;
      border: none;
      border-radius: var(--radius-sm);
      font-size: .9rem;
      font-weight: 600;
      cursor: pointer;
      transition: background var(--transition);
      margin-top: .5rem;
    }

    .btn-login:hover { background: var(--color-primary-dark); }

    .alert-error {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #b91c1c;
      border-radius: var(--radius-sm);
      padding: .75rem 1rem;
      font-size: .85rem;
      margin-bottom: 1.25rem;
    }

    .login-footer {
      text-align: center;
      margin-top: 1.5rem;
      font-size: .75rem;
      color: var(--color-text-muted);
    }
  </style>
</head>
<body>
  <div class="login-wrapper">

    <!-- Logo / Cabecera -->
    <div class="login-logo">
      <h1><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></h1>
      <p>Panel de gestión del hackathon</p>
    </div>

    <!-- Tarjeta de login -->
    <div class="login-card">
      <h2>Iniciar sesión</h2>

      <?php if (!empty($error)): ?>
      <div class="alert-error" role="alert">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
      </div>
      <?php endif; ?>

      <form method="POST"
            action="<?= BASE_URL ?>/?module=auth&action=store"
            autocomplete="off"
            novalidate>

        <!-- Token CSRF oculto -->
        <input type="hidden" name="_csrf_token"
               value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-group">
          <label for="username">Usuario</label>
          <input type="text"
                 id="username"
                 name="username"
                 value="<?= htmlspecialchars($username ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="nombre_usuario"
                 required
                 autofocus
                 autocomplete="username">
        </div>

        <div class="form-group">
          <label for="password">Contraseña</label>
          <input type="password"
                 id="password"
                 name="password"
                 placeholder="••••••••"
                 required
                 autocomplete="current-password">
        </div>

        <button type="submit" class="btn-login">Entrar</button>
      </form>
    </div>

    <div class="login-footer">
      <?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?> v<?= APP_VERSION ?>
    </div>

  </div>
</body>
</html>
