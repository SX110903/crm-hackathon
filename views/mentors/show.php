<div class="page-header">
  <div>
    <h2><?= htmlspecialchars($mentor['FirstName'] . ' ' . $mentor['LastName'], ENT_QUOTES, 'UTF-8') ?></h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=mentors">Mentores</a> &rsaquo; Detalle</div>
  </div>
  <div class="btn-group">
    <a href="<?= BASE_URL ?>/?module=mentors&action=edit&id=<?= $mentor['MentorID'] ?>" class="btn btn-secondary">Editar</a>
    <a href="<?= BASE_URL ?>/?module=mentors" class="btn btn-secondary">← Volver</a>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header"><h3>Información del mentor</h3></div>
  <div class="card-body">
    <div class="detail-grid">
      <div class="detail-item"><span class="detail-label">Email</span><span class="detail-value"><?= htmlspecialchars($mentor['Email'], ENT_QUOTES, 'UTF-8') ?></span></div>
      <div class="detail-item"><span class="detail-label">Empresa</span><span class="detail-value"><?= htmlspecialchars($mentor['Company'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
      <div class="detail-item"><span class="detail-label">Especialización</span><span class="detail-value"><span class="badge badge-purple"><?= htmlspecialchars($mentor['Specialization'], ENT_QUOTES, 'UTF-8') ?></span></span></div>
      <div class="detail-item"><span class="detail-label">Slots disponibles</span><span class="detail-value"><?= $mentor['AvailableSlots'] ?></span></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3>Sesiones de mentoría (<?= count($sessions) ?>)</h3></div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>Equipo</th><th>Fecha</th><th>Duración</th><th>Tema</th><th>Notas</th></tr>
      </thead>
      <tbody>
        <?php if (empty($sessions)): ?>
        <tr><td colspan="5"><div class="empty-state"><p>Sin sesiones registradas.</p></div></td></tr>
        <?php else: ?>
        <?php foreach ($sessions as $session): ?>
        <tr>
          <td><?= htmlspecialchars($session['TeamName'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-sm"><?= htmlspecialchars(substr($session['SessionDate'] ?? '', 0, 16), ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-sm"><?= $session['Duration'] ?> min</td>
          <td class="text-sm"><?= htmlspecialchars($session['Topic'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-sm text-muted"><?= htmlspecialchars($session['Notes'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
