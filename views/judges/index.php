<?php $module = 'judges'; ?>
<div class="page-header">
  <div>
    <h2>Jueces</h2>
    <div class="breadcrumb">Panel de jueces evaluadores</div>
  </div>
  <a href="<?= BASE_URL ?>/?module=judges&action=create" class="btn btn-primary">+ Nuevo juez</a>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Nombre</th><th>Empresa</th><th>Expertise</th>
          <th>Experiencia</th><th>Evaluaciones</th><th>Puntuación media dada</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($judges)): ?>
        <tr><td colspan="8"><div class="empty-state"><div class="empty-icon">◈</div><p>No hay jueces registrados.</p></div></td></tr>
        <?php else: ?>
        <?php foreach ($judges as $judge): ?>
        <tr>
          <td class="text-muted text-sm">#<?= $judge['JudgeID'] ?></td>
          <td>
            <a href="<?= BASE_URL ?>/?module=judges&action=show&id=<?= $judge['JudgeID'] ?>">
              <?= htmlspecialchars($judge['FirstName'] . ' ' . $judge['LastName'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </td>
          <td class="text-sm"><?= htmlspecialchars($judge['Company'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td><span class="badge badge-blue"><?= htmlspecialchars($judge['Expertise'], ENT_QUOTES, 'UTF-8') ?></span></td>
          <td class="text-center"><?= $judge['YearsOfExperience'] ?> años</td>
          <td class="text-center"><?= $judge['totalEvaluations'] ?></td>
          <td class="font-bold"><?= $judge['avgScoreGiven'] !== null ? number_format($judge['avgScoreGiven'], 2) : '—' ?></td>
          <td>
            <div class="btn-group">
              <a href="<?= BASE_URL ?>/?module=judges&action=edit&id=<?= $judge['JudgeID'] ?>" class="btn btn-secondary btn-xs">Editar</a>
              <form method="POST" action="<?= BASE_URL ?>/?module=judges&action=delete&id=<?= $judge['JudgeID'] ?>" class="form-delete" style="display:inline">
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
