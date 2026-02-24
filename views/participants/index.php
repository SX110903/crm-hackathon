<?php $module = 'participants'; ?>
<div class="page-header">
  <div>
    <h2>Participantes</h2>
    <div class="breadcrumb">Registro de participantes del hackathon</div>
  </div>
  <a href="<?= BASE_URL ?>/?module=participants&action=create" class="btn btn-primary">+ Nuevo participante</a>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Universidad</th>
          <th>Carrera</th>
          <th>Año</th>
          <th>Equipo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($participants)): ?>
        <tr><td colspan="8"><div class="empty-state"><div class="empty-icon">◉</div><p>No hay participantes registrados.</p></div></td></tr>
        <?php else: ?>
        <?php foreach ($participants as $p): ?>
        <tr>
          <td class="text-muted text-sm">#<?= $p['ParticipantID'] ?></td>
          <td>
            <a href="<?= BASE_URL ?>/?module=participants&action=show&id=<?= $p['ParticipantID'] ?>">
              <?= htmlspecialchars($p['FirstName'] . ' ' . $p['LastName'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </td>
          <td class="text-sm"><?= htmlspecialchars($p['Email'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-sm"><?= htmlspecialchars($p['University'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-sm"><?= htmlspecialchars($p['Major'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-center"><?= $p['YearOfStudy'] ?></td>
          <td class="text-sm">
            <?php if ($p['TeamName']): ?>
              <span class="badge badge-blue"><?= htmlspecialchars($p['TeamName'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php else: ?>
              <span class="text-muted">—</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="btn-group">
              <a href="<?= BASE_URL ?>/?module=participants&action=edit&id=<?= $p['ParticipantID'] ?>" class="btn btn-secondary btn-xs">Editar</a>
              <form method="POST" action="<?= BASE_URL ?>/?module=participants&action=delete&id=<?= $p['ParticipantID'] ?>" class="form-delete" style="display:inline">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
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
