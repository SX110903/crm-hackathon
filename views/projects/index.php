<?php
$module = 'projects';

function projectStatusBadge(string $status): string {
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
    <h2>Proyectos</h2>
    <div class="breadcrumb">Listado y ranking de proyectos</div>
  </div>
  <a href="<?= BASE_URL ?>/?module=projects&action=create" class="btn btn-primary">+ Nuevo proyecto</a>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Proyecto</th>
          <th>Equipo</th>
          <th>Categoría</th>
          <th>Estado</th>
          <th>Evaluaciones</th>
          <th>Puntuación</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($projects)): ?>
        <tr><td colspan="8"><div class="empty-state"><div class="empty-icon">◇</div><p>No hay proyectos registrados.</p></div></td></tr>
        <?php else: ?>
        <?php foreach ($projects as $project): ?>
        <tr>
          <td class="text-muted text-sm">#<?= $project['ProjectID'] ?></td>
          <td>
            <a href="<?= BASE_URL ?>/?module=projects&action=show&id=<?= $project['ProjectID'] ?>">
              <?= htmlspecialchars($project['ProjectName'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </td>
          <td class="text-sm"><?= htmlspecialchars($project['TeamName'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><span class="badge badge-gray"><?= htmlspecialchars($project['Category'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></td>
          <td><?= projectStatusBadge($project['Status'] ?? '') ?></td>
          <td class="text-center"><?= (int) $project['totalEvaluations'] ?></td>
          <td>
            <?php if ($project['avgScore'] !== null): ?>
            <div class="score-bar">
              <div class="score-bar-track">
                <div class="score-bar-fill" style="width:<?= min(100, ($project['avgScore'] / 10) * 100) ?>%"></div>
              </div>
              <span class="score-bar-value"><?= number_format($project['avgScore'], 1) ?></span>
            </div>
            <?php else: ?>
            <span class="text-muted">—</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="btn-group">
              <a href="<?= BASE_URL ?>/?module=projects&action=edit&id=<?= $project['ProjectID'] ?>" class="btn btn-secondary btn-xs">Editar</a>
              <a href="<?= BASE_URL ?>/?module=evaluations&action=create&project_id=<?= $project['ProjectID'] ?>" class="btn btn-warning btn-xs">Evaluar</a>
              <form method="POST" action="<?= BASE_URL ?>/?module=projects&action=delete&id=<?= $project['ProjectID'] ?>" class="form-delete" style="display:inline">
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
