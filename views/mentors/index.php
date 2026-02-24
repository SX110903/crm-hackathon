<?php $module = 'mentors'; ?>
<div class="page-header">
  <div>
    <h2>Mentores</h2>
    <div class="breadcrumb">Gestión de mentores y sesiones</div>
  </div>
  <a href="<?= BASE_URL ?>/?module=mentors&action=create" class="btn btn-primary">+ Nuevo mentor</a>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Nombre</th><th>Empresa</th><th>Especialización</th>
          <th>Slots</th><th>Sesiones</th><th>Horas</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($mentors)): ?>
        <tr><td colspan="8"><div class="empty-state"><div class="empty-icon">◆</div><p>No hay mentores registrados.</p></div></td></tr>
        <?php else: ?>
        <?php foreach ($mentors as $mentor): ?>
        <tr>
          <td class="text-muted text-sm">#<?= $mentor['MentorID'] ?></td>
          <td>
            <a href="<?= BASE_URL ?>/?module=mentors&action=show&id=<?= $mentor['MentorID'] ?>">
              <?= htmlspecialchars($mentor['FirstName'] . ' ' . $mentor['LastName'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </td>
          <td class="text-sm"><?= htmlspecialchars($mentor['Company'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td><span class="badge badge-purple"><?= htmlspecialchars($mentor['Specialization'], ENT_QUOTES, 'UTF-8') ?></span></td>
          <td class="text-center"><?= $mentor['AvailableSlots'] ?></td>
          <td class="text-center"><?= $mentor['totalSessions'] ?></td>
          <td class="text-center"><?= number_format($mentor['totalMinutes'] / 60, 1) ?>h</td>
          <td>
            <div class="btn-group">
              <a href="<?= BASE_URL ?>/?module=mentors&action=edit&id=<?= $mentor['MentorID'] ?>" class="btn btn-secondary btn-xs">Editar</a>
              <form method="POST" action="<?= BASE_URL ?>/?module=mentors&action=delete&id=<?= $mentor['MentorID'] ?>" class="form-delete" style="display:inline">
                <button type="submit" class="btn btn-danger btn-xs">Eliminar</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if (!empty($pagination)): ?>
  <div class="card-footer"><?php include ROOT_PATH . '/views/layout/_pagination.php'; ?></div>
  <?php endif; ?>
</div>
