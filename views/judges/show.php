<div class="page-header">
  <div>
    <h2><?= htmlspecialchars($judge['FirstName'] . ' ' . $judge['LastName'], ENT_QUOTES, 'UTF-8') ?></h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=judges">Jueces</a> &rsaquo; Detalle</div>
  </div>
  <div class="btn-group">
    <a href="<?= BASE_URL ?>/?module=judges&action=edit&id=<?= $judge['JudgeID'] ?>" class="btn btn-secondary">Editar</a>
    <a href="<?= BASE_URL ?>/?module=judges" class="btn btn-secondary">← Volver</a>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header"><h3>Información del juez</h3></div>
  <div class="card-body">
    <div class="detail-grid">
      <div class="detail-item"><span class="detail-label">Email</span><span class="detail-value"><?= htmlspecialchars($judge['Email'], ENT_QUOTES, 'UTF-8') ?></span></div>
      <div class="detail-item"><span class="detail-label">Empresa</span><span class="detail-value"><?= htmlspecialchars($judge['Company'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
      <div class="detail-item"><span class="detail-label">Expertise</span><span class="detail-value"><span class="badge badge-blue"><?= htmlspecialchars($judge['Expertise'], ENT_QUOTES, 'UTF-8') ?></span></span></div>
      <div class="detail-item"><span class="detail-label">Años de experiencia</span><span class="detail-value"><?= $judge['YearsOfExperience'] ?></span></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3>Evaluaciones realizadas (<?= count($evaluations) ?>)</h3></div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>Proyecto</th><th>Equipo</th><th>Innovación</th><th>Técnica</th><th>Presentación</th><th>Usabilidad</th><th>Total</th><th>Fecha</th></tr>
      </thead>
      <tbody>
        <?php if (empty($evaluations)): ?>
        <tr><td colspan="8"><div class="empty-state"><p>Sin evaluaciones realizadas.</p></div></td></tr>
        <?php else: ?>
        <?php foreach ($evaluations as $eval): ?>
        <tr>
          <td>
            <a href="<?= BASE_URL ?>/?module=projects&action=show&id=<?= $eval['ProjectID'] ?>">
              <?= htmlspecialchars($eval['ProjectName'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </td>
          <td class="text-sm text-muted"><?= htmlspecialchars($eval['TeamName'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= number_format($eval['InnovationScore'], 1) ?></td>
          <td><?= number_format($eval['TechnicalScore'], 1) ?></td>
          <td><?= number_format($eval['PresentationScore'], 1) ?></td>
          <td><?= number_format($eval['UsabilityScore'], 1) ?></td>
          <td class="font-bold"><?= number_format($eval['TotalScore'], 2) ?></td>
          <td class="text-sm text-muted"><?= htmlspecialchars(substr($eval['EvaluationDate'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
