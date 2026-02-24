<?php $module = 'teams'; ?>
<div class="page-header">
  <div>
    <h2>Equipos</h2>
    <div class="breadcrumb">Gestión de equipos participantes</div>
  </div>
  <a href="<?= BASE_URL ?>/?module=teams&action=create" class="btn btn-primary">+ Nuevo equipo</a>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Líder</th>
          <th>Miembros</th>
          <th>Proyectos</th>
          <th>Puntuación</th>
          <th>Creado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($teams)): ?>
        <tr><td colspan="8"><div class="empty-state"><div class="empty-icon">◎</div><p>No hay equipos registrados.</p></div></td></tr>
        <?php else: ?>
        <?php foreach ($teams as $team): ?>
        <tr>
          <td class="text-muted text-sm">#<?= $team['TeamID'] ?></td>
          <td>
            <a href="<?= BASE_URL ?>/?module=teams&action=show&id=<?= $team['TeamID'] ?>">
              <?= htmlspecialchars($team['TeamName'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </td>
          <td class="text-sm"><?= htmlspecialchars($team['leaderName'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-center"><?= (int)($team['TotalMembers'] ?? 0) ?>/<?= $team['MaxMembers'] ?></td>
          <td class="text-center"><?= $team['projectCount'] ?></td>
          <td class="font-bold"><?= $team['avgScore'] > 0 ? number_format($team['avgScore'], 1) : '—' ?></td>
          <td class="text-sm text-muted"><?= htmlspecialchars(substr($team['CreatedDate'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
          <td>
            <div class="btn-group">
              <a href="<?= BASE_URL ?>/?module=teams&action=edit&id=<?= $team['TeamID'] ?>" class="btn btn-secondary btn-xs">Editar</a>
              <form method="POST" action="<?= BASE_URL ?>/?module=teams&action=delete&id=<?= $team['TeamID'] ?>" class="form-delete" style="display:inline">
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
