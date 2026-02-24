<?php
function projStatusBadge(string $status): string {
    $map = ['In Progress'=>'badge-blue','Submitted'=>'badge-yellow','Under Review'=>'badge-purple','Awarded'=>'badge-green','Rejected'=>'badge-red'];
    return '<span class="badge ' . ($map[$status] ?? 'badge-gray') . '">' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . '</span>';
}
function scoreBar(float $score, float $max = 10): string {
    $pct = min(100, ($score / $max) * 100);
    return '<div class="score-bar"><div class="score-bar-track"><div class="score-bar-fill" style="width:' . $pct . '%"></div></div>'
         . '<span class="score-bar-value">' . number_format($score, 1) . '</span></div>';
}
?>
<div class="page-header">
  <div>
    <h2><?= htmlspecialchars($project['ProjectName'], ENT_QUOTES, 'UTF-8') ?></h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=projects">Proyectos</a> &rsaquo; Detalle</div>
  </div>
  <div class="btn-group">
    <a href="<?= BASE_URL ?>/?module=evaluations&action=create&project_id=<?= $project['ProjectID'] ?>" class="btn btn-warning">+ Evaluar</a>
    <a href="<?= BASE_URL ?>/?module=projects&action=edit&id=<?= $project['ProjectID'] ?>" class="btn btn-secondary">Editar</a>
    <a href="<?= BASE_URL ?>/?module=projects" class="btn btn-secondary">← Volver</a>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header">
    <h3>Información del proyecto</h3>
    <?= projStatusBadge($project['Status'] ?? '') ?>
  </div>
  <div class="card-body">
    <div class="detail-grid">
      <div class="detail-item">
        <span class="detail-label">Equipo</span>
        <span class="detail-value">
          <a href="<?= BASE_URL ?>/?module=teams&action=show&id=<?= $project['TeamID'] ?>">
            <?= htmlspecialchars($project['TeamName'], ENT_QUOTES, 'UTF-8') ?>
          </a>
        </span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Líder</span>
        <span class="detail-value"><?= htmlspecialchars($project['leaderName'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Categoría</span>
        <span class="detail-value"><span class="badge badge-gray"><?= htmlspecialchars($project['Category'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Evaluaciones</span>
        <span class="detail-value"><?= $project['totalEvaluations'] ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Puntuación media</span>
        <span class="detail-value font-bold" style="font-size:1.2rem"><?= $project['avgScore'] !== null ? number_format($project['avgScore'], 2) : '—' ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">GitHub</span>
        <span class="detail-value">
          <?php if ($project['GitHubURL']): ?>
            <a href="<?= htmlspecialchars($project['GitHubURL'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Ver repositorio</a>
          <?php else: ?>—<?php endif; ?>
        </span>
      </div>
      <div class="detail-item full">
        <span class="detail-label">Stack tecnológico</span>
        <span class="detail-value"><?= htmlspecialchars($project['TechnologyStack'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <?php if ($project['Description']): ?>
      <div class="detail-item full">
        <span class="detail-label">Descripción</span>
        <span class="detail-value"><?= nl2br(htmlspecialchars($project['Description'], ENT_QUOTES, 'UTF-8')) ?></span>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ─── Evaluaciones ──────────────────────────────────────────────────────── -->
<div class="card">
  <div class="card-header">
    <h3>Evaluaciones (<?= count($evaluations) ?>)</h3>
    <a href="<?= BASE_URL ?>/?module=evaluations&action=create&project_id=<?= $project['ProjectID'] ?>" class="btn btn-primary btn-sm">+ Nueva evaluación</a>
  </div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Juez</th>
          <th>Empresa</th>
          <th>Innovación</th>
          <th>Técnica</th>
          <th>Presentación</th>
          <th>Usabilidad</th>
          <th>Total</th>
          <th>Fecha</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($evaluations)): ?>
        <tr><td colspan="9"><div class="empty-state"><p>Sin evaluaciones aún. <a href="<?= BASE_URL ?>/?module=evaluations&action=create&project_id=<?= $project['ProjectID'] ?>">Añadir primera evaluación</a></p></div></td></tr>
        <?php else: ?>
        <?php foreach ($evaluations as $eval): ?>
        <tr>
          <td><?= htmlspecialchars($eval['judgeName'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-sm text-muted"><?= htmlspecialchars($eval['Company'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= number_format($eval['InnovationScore'], 1) ?></td>
          <td><?= number_format($eval['TechnicalScore'], 1) ?></td>
          <td><?= number_format($eval['PresentationScore'], 1) ?></td>
          <td><?= number_format($eval['UsabilityScore'], 1) ?></td>
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
</div>
