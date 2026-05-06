<?php
// ─── Helper de badge de estado ───────────────────────────────────────────────
function statusBadge(string $status): string {
    $map = [
        'In Progress' => 'badge-blue',
        'Submitted'   => 'badge-yellow',
        'Under Review'=> 'badge-purple',
        'Awarded'     => 'badge-green',
        'Rejected'    => 'badge-red',
    ];
    $class = $map[$status] ?? 'badge-gray';
    return '<span class="badge ' . $class . '">' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . '</span>';
}
?>
<div class="page-header">
  <div>
    <h2>Dashboard</h2>
    <div class="breadcrumb">Vista general del hackathon</div>
  </div>
</div>

<?php if (!empty($flash)): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
  <?= $flash['message'] ?>
</div>
<?php endif; ?>

<!-- ─── Tarjetas de estadísticas ─────────────────────────────────────────── -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-value"><?= $stats['participants'] ?></div>
    <div class="stat-label">Participantes</div>
  </div>
  <div class="stat-card blue">
    <div class="stat-value"><?= $stats['teams'] ?></div>
    <div class="stat-label">Equipos</div>
  </div>
  <div class="stat-card orange">
    <div class="stat-value"><?= $stats['projects'] ?></div>
    <div class="stat-label">Proyectos</div>
  </div>
  <div class="stat-card green">
    <div class="stat-value"><?= $stats['evaluations'] ?></div>
    <div class="stat-label">Evaluaciones</div>
  </div>
  <div class="stat-card">
    <div class="stat-value"><?= $stats['mentors'] ?></div>
    <div class="stat-label">Mentores</div>
  </div>
  <div class="stat-card blue">
    <div class="stat-value"><?= $stats['judges'] ?></div>
    <div class="stat-label">Jueces</div>
  </div>
  <div class="stat-card orange">
    <div class="stat-value"><?= $stats['mentoringSessions'] ?></div>
    <div class="stat-label">Sesiones Mentoría</div>
  </div>
  <div class="stat-card green">
    <div class="stat-value"><?= $stats['awardsAssigned'] ?>/<?= $stats['awards'] ?></div>
    <div class="stat-label">Premios Asignados</div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;flex-wrap:wrap;">

<!-- ─── Ranking de proyectos ─────────────────────────────────────────────── -->
<div class="card">
  <div class="card-header">
    <h3>Top Proyectos</h3>
    <a href="<?= BASE_URL ?>/?module=projects" class="btn btn-secondary btn-sm">Ver todos</a>
  </div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Proyecto</th>
          <th>Equipo</th>
          <th>Puntuación</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($rankings)): ?>
        <tr><td colspan="5" class="text-center text-muted" style="padding:2rem">Sin evaluaciones aún</td></tr>
        <?php else: ?>
        <?php foreach ($rankings as $i => $row): ?>
        <tr>
          <td class="text-muted"><?= $i + 1 ?></td>
          <td>
            <a href="<?= BASE_URL ?>/?module=projects&action=show&id=<?= $row['ProjectID'] ?>">
              <?= htmlspecialchars($row['ProjectName'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </td>
          <td class="text-muted text-sm"><?= htmlspecialchars($row['TeamName'], ENT_QUOTES, 'UTF-8') ?></td>
          <td>
            <div class="score-bar">
              <div class="score-bar-track">
                <div class="score-bar-fill" style="width:<?= min(100, ($row['avgScore'] / 10) * 100) ?>%"></div>
              </div>
              <span class="score-bar-value"><?= $row['avgScore'] !== null ? number_format($row['avgScore'], 1) : '—' ?></span>
            </div>
          </td>
          <td><?= statusBadge($row['Status']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ─── Top Equipos ───────────────────────────────────────────────────────── -->
<div class="card">
  <div class="card-header">
    <h3>Top Equipos</h3>
    <a href="<?= BASE_URL ?>/?module=teams" class="btn btn-secondary btn-sm">Ver todos</a>
  </div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Equipo</th>
          <th>Líder</th>
          <th>Miembros</th>
          <th>Puntuación</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($topTeams)): ?>
        <tr><td colspan="4" class="text-center text-muted" style="padding:2rem">Sin datos</td></tr>
        <?php else: ?>
        <?php foreach ($topTeams as $team): ?>
        <tr>
          <td>
            <a href="<?= BASE_URL ?>/?module=teams&action=show&id=<?= $team['TeamID'] ?>">
              <?= htmlspecialchars($team['TeamName'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </td>
          <td class="text-sm text-muted"><?= htmlspecialchars($team['leaderName'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-center"><?= (int)($team['TotalMembers'] ?? 0) ?></td>
          <td class="font-bold"><?= $team['avgScore'] > 0 ? number_format($team['avgScore'], 1) : '—' ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</div><!-- /grid 2 cols -->

<!-- ─── Eventos recientes ─────────────────────────────────────────────────── -->
<div class="card" style="margin-top:1.5rem">
  <div class="card-header">
    <h3>Eventos Recientes</h3>
  </div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Tipo</th>
          <th>Descripción</th>
          <th>Equipo</th>
          <th>Proyecto</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($recentEvents)): ?>
        <tr><td colspan="5" class="text-center text-muted" style="padding:2rem">Sin eventos registrados</td></tr>
        <?php else: ?>
        <?php foreach ($recentEvents as $event): ?>
        <?php
          $eventBadge = [
            'TEAM_CREATED'      => 'badge-blue',
            'MEMBER_JOINED'     => 'badge-blue',
            'PROJECT_SUBMITTED' => 'badge-yellow',
            'PROJECT_EVALUATED' => 'badge-purple',
            'AWARD_GRANTED'     => 'badge-green',
          ][$event['EventType']] ?? 'badge-gray';
        ?>
        <tr>
          <td><span class="badge <?= $eventBadge ?>"><?= htmlspecialchars($event['EventType'], ENT_QUOTES, 'UTF-8') ?></span></td>
          <td class="text-sm"><?= htmlspecialchars($event['Description'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-sm text-muted"><?= htmlspecialchars($event['TeamName'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-sm text-muted"><?= htmlspecialchars($event['ProjectName'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-sm text-muted"><?= htmlspecialchars($event['EventDate'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
