<?php
// $currentPage viene del BaseController::render()
$active = $currentPage ?? 'dashboard';

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

function sidebarUrl(string $module): string {
    return BASE_URL . '/?module=' . $module;

}

?>
<aside class="sidebar">
  <div class="sidebar-logo">
    <h1><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></h1>
    <span>v<?= APP_VERSION ?> &mdash; Gestión Hackathon</span>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-title">Navegación</div>
    <?php foreach ($navItems as $module => $item): ?>
      <a href="<?= sidebarUrl($module) ?>"
         class="nav-link <?= $active === $module ? 'active' : '' ?>">
        <span class="icon"><?= $item['icon'] ?></span>
        <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
      </a>
    <?php endforeach; ?>
  </nav>

  <div style="padding:.75rem 1rem;border-top:1px solid #1e293b;">
    <span style="font-size:.7rem;color:#475569;">HackathonDB &middot; Puerto 3307</span>
  </div>
</aside>
