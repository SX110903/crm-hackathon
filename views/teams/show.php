<?php
function statusBadgeTeam(string $status): string {
    $map = [
        'In Progress'  => 'badge-blue',
        'Submitted'    => 'badge-yellow',
        'Under Review' => 'badge-purple',
        'Awarded'      => 'badge-green',
        'Rejected'     => 'badge-red',
    ];
    return '<span class="badge ' . ($map[$status] ?? 'badge-gray') . '">'
        . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . '</span>';
}
?>
<div class="page-header">
  <div>
    <h2><?= htmlspecialchars($team['TeamName'], ENT_QUOTES, 'UTF-8') ?></h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=teams">Equipos</a> &rsaquo; Detalle</div>
  </div>
  <div class="btn-group">
    <a href="<?= BASE_URL ?>/?module=teams&action=edit&id=<?= $team['TeamID'] ?>" class="btn btn-secondary">Editar</a>
    <a href="<?= BASE_URL ?>/?module=teams" class="btn btn-secondary">← Volver</a>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<!-- ─── Info del equipo ───────────────────────────────────────────────────── -->
<div class="card">
  <div class="card-header"><h3>Información del equipo</h3></div>
  <div class="card-body">
    <div class="detail-grid">
      <div class="detail-item">
        <span class="detail-label">Líder</span>
        <span class="detail-value"><?= htmlspecialchars($team['leaderName'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Email líder</span>
        <span class="detail-value"><?= htmlspecialchars($team['leaderEmail'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Miembros</span>
        <span class="detail-value"><?= (int)($team['TotalMembers'] ?? 0) ?> / <?= $team['MaxMembers'] ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Puntuación media</span>
        <span class="detail-value font-bold"><?= $team['avgScore'] > 0 ? number_format($team['avgScore'], 2) : '—' ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Horas de mentoría</span>
        <span class="detail-value"><?= number_format($team['TotalMentoringHours'] ?? 0, 1) ?>h</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Sesiones de mentoría</span>
        <span class="detail-value"><?= (int)($team['TotalMentoringSessions'] ?? 0) ?></span>
      </div>
    </div>
  </div>
</div>

<!-- ─── Miembros ──────────────────────────────────────────────────────────── -->
<div class="card">
  <div class="card-header">
    <h3>Miembros (<?= count($members) ?>)</h3>
    <a href="<?= BASE_URL ?>/?module=participants&action=create" class="btn btn-primary btn-sm">+ Añadir participante</a>
  </div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>Nombre</th><th>Email</th><th>Universidad</th><th>Rol</th><th>Fecha ingreso</th></tr>
      </thead>
      <tbody>
        <?php if (empty($members)): ?>
        <tr><td colspan="5"><div class="empty-state"><p>Sin miembros registrados.</p></div></td></tr>
        <?php else: ?>
        <?php foreach ($members as $member): ?>
        <tr>
          <td>
            <a href="<?= BASE_URL ?>/?module=participants&action=show&id=<?= $member['ParticipantID'] ?>">
              <?= htmlspecialchars($member['memberName'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </td>
          <td class="text-sm text-muted"><?= htmlspecialchars($member['Email'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-sm"><?= htmlspecialchars($member['University'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><span class="badge badge-blue"><?= htmlspecialchars($member['Role'], ENT_QUOTES, 'UTF-8') ?></span></td>
          <td class="text-sm text-muted"><?= htmlspecialchars(substr($member['JoinedDate'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ─── Proyecto ─────────────────────────────────────────────────────────── -->
<div class="card">
  <div class="card-header">
    <h3>Proyecto</h3>
    <a href="<?= BASE_URL ?>/?module=projects&action=create" class="btn btn-primary btn-sm">+ Nuevo proyecto</a>
  </div>
  <div class="card-body">
    <?php if (!$project): ?>
    <div class="empty-state"><p>Este equipo no tiene proyecto registrado.</p></div>
    <?php else: ?>
    <div class="detail-grid">
      <div class="detail-item">
        <span class="detail-label">Nombre</span>
        <span class="detail-value">
          <a href="<?= BASE_URL ?>/?module=projects&action=show&id=<?= $project['ProjectID'] ?>">
            <?= htmlspecialchars($project['ProjectName'], ENT_QUOTES, 'UTF-8') ?>
          </a>
        </span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Estado</span>
        <span class="detail-value"><?= statusBadgeTeam($project['Status'] ?? '') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Categoría</span>
        <span class="detail-value"><?= htmlspecialchars($project['Category'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Puntuación media</span>
        <span class="detail-value font-bold"><?= $project['avgScore'] !== null ? number_format($project['avgScore'], 2) : '—' ?></span>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- ─── Sesiones de mentoría ─────────────────────────────────────────────── -->
<div class="card">
  <div class="card-header"><h3>Sesiones de mentoría (<?= count($sessions) ?>)</h3></div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>Mentor</th><th>Especialización</th><th>Fecha</th><th>Duración</th><th>Tema</th></tr>
      </thead>
      <tbody>
        <?php if (empty($sessions)): ?>
        <tr><td colspan="5"><div class="empty-state"><p>Sin sesiones registradas.</p></div></td></tr>
        <?php else: ?>
        <?php foreach ($sessions as $session): ?>
        <tr>
          <td><?= htmlspecialchars($session['mentorName'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-sm text-muted"><?= htmlspecialchars($session['Specialization'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-sm"><?= htmlspecialchars(substr($session['SessionDate'] ?? '', 0, 16), ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-sm"><?= $session['Duration'] ?> min</td>
          <td class="text-sm"><?= htmlspecialchars($session['Topic'], ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
