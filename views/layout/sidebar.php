<?php
// $currentPage y $authUser vienen del BaseController::render()
$active   = $currentPage ?? 'dashboard';
$authUser = $authUser    ?? null;

$navItems = [
    'dashboard'    => ['label' => 'Dashboard',      'icon' => '◈'],
    'participants' => ['label' => 'Participantes',   'icon' => '◉'],
    'teams'        => ['label' => 'Equipos',         'icon' => '◎'],
    'projects'     => ['label' => 'Proyectos',       'icon' => '◇'],
    'mentors'      => ['label' => 'Mentores',        'icon' => '◆'],
    'judges'       => ['label' => 'Jueces',          'icon' => '◈'],
    'evaluations'  => ['label' => 'Evaluaciones',    'icon' => '◉'],
    'awards'       => ['label' => 'Premios',         'icon' => '★'],
];
?>
<aside class="sidebar">
  <div class="sidebar-logo">
    <h1><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></h1>
    <span>v<?= APP_VERSION ?> &mdash; Gestión Hackathon</span>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-title">Navegación</div>
    <?php foreach ($navItems as $module => $item): ?>
      <a href="<?= BASE_URL ?>/?module=<?= $module ?>"
         class="nav-link <?= $active === $module ? 'active' : '' ?>">
        <span class="icon"><?= $item['icon'] ?></span>
        <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
      </a>
    <?php endforeach; ?>
  </nav>

  <!-- ─── Usuario autenticado + logout ──────────────────────────────────── -->
  <?php if ($authUser): ?>
  <div style="margin-top:auto;padding:.75rem 1rem;border-top:1px solid #1e293b;">
    <div style="font-size:.72rem;color:#94a3b8;margin-bottom:.4rem;">
      <span style="display:block;font-weight:600;color:#cbd5e1;">
        <?= htmlspecialchars($authUser['fullName'], ENT_QUOTES, 'UTF-8') ?>
      </span>
      <span><?= htmlspecialchars($authUser['username'], ENT_QUOTES, 'UTF-8') ?></span>
      &middot;
      <span style="color:#60a5fa;"><?= htmlspecialchars($authUser['role'], ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <form method="POST"
          action="<?= BASE_URL ?>/?module=auth&action=logout"
          style="margin:0;">
      <input type="hidden" name="_csrf_token"
             value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <button type="submit"
              style="width:100%;padding:.4rem .75rem;background:#1e293b;color:#94a3b8;
                     border:1px solid #334155;border-radius:4px;font-size:.75rem;
                     cursor:pointer;text-align:left;transition:background 150ms ease;">
        ⎋ Cerrar sesión
      </button>
    </form>
  </div>
  <?php else: ?>
  <div style="padding:.75rem 1rem;border-top:1px solid #1e293b;">
    <span style="font-size:.7rem;color:#475569;">HackathonDB &middot; Puerto <?= DB_PORT ?></span>
  </div>
  <?php endif; ?>
</aside>
