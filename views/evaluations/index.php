<?php $module = 'evaluations'; ?>
<div class="page-header">
  <div>
    <h2>Evaluaciones</h2>
    <div class="breadcrumb">Registro de evaluaciones por jueces</div>
  </div>
  <a href="<?= BASE_URL ?>/?module=evaluations&action=create" class="btn btn-primary">+ Nueva evaluación</a>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Proyecto</th><th>Juez</th>
          <th>Innovación</th><th>Técnica</th><th>Presentación</th><th>Usabilidad</th>
          <th>Total</th><th>Fecha</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($evaluations)): ?>
        <tr><td colspan="10"><div class="empty-state"><div class="empty-icon">◉</div><p>No hay evaluaciones registradas.</p></div></td></tr>
        <?php else: ?>
        <?php foreach ($evaluations as $eval): ?>
        <tr>
          <td class="text-muted text-sm">#<?= $eval['EvaluationID'] ?></td>
          <td class="text-sm"><?= htmlspecialchars($eval['ProjectName'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-sm"><?= htmlspecialchars($eval['judgeName'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-center"><?= number_format($eval['InnovationScore'], 1) ?></td>
          <td class="text-center"><?= number_format($eval['TechnicalScore'], 1) ?></td>
          <td class="text-center"><?= number_format($eval['PresentationScore'], 1) ?></td>
          <td class="text-center"><?= number_format($eval['UsabilityScore'], 1) ?></td>
          <td class="font-bold"><?= number_format($eval['TotalScore'], 2) ?></td>
          <td class="text-sm text-muted"><?= htmlspecialchars(substr($eval['EvaluationDate'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
          <td>
            <form method="POST" action="<?= BASE_URL ?>/?module=evaluations&action=delete&id=<?= $eval['EvaluationID'] ?>" class="form-delete" style="display:inline">
              <button type="submit" class="btn btn-danger btn-xs">Eliminar</button>
            </form>
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
