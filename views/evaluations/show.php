<div class="page-header">
  <div>
    <h2>Evaluación #<?= $evaluation['EvaluationID'] ?></h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=evaluations">Evaluaciones</a> &rsaquo; Detalle</div>
  </div>
  <a href="<?= BASE_URL ?>/?module=evaluations" class="btn btn-secondary">← Volver</a>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header"><h3>Resultado de evaluación</h3></div>
  <div class="card-body">
    <div class="detail-grid">
      <div class="detail-item"><span class="detail-label">Proyecto</span>
        <span class="detail-value"><a href="<?= BASE_URL ?>/?module=projects&action=show&id=<?= $evaluation['ProjectID'] ?>"><?= htmlspecialchars($evaluation['ProjectName'], ENT_QUOTES, 'UTF-8') ?></a></span>
      </div>
      <div class="detail-item"><span class="detail-label">Equipo</span><span class="detail-value"><?= htmlspecialchars($evaluation['TeamName'], ENT_QUOTES, 'UTF-8') ?></span></div>
      <div class="detail-item"><span class="detail-label">Juez</span><span class="detail-value"><?= htmlspecialchars($evaluation['judgeName'], ENT_QUOTES, 'UTF-8') ?></span></div>
      <div class="detail-item"><span class="detail-label">Empresa del juez</span><span class="detail-value"><?= htmlspecialchars($evaluation['Company'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
      <div class="detail-item"><span class="detail-label">Fecha</span><span class="detail-value"><?= htmlspecialchars(substr($evaluation['EvaluationDate'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></span></div>
      <div class="detail-item"><span class="detail-label">Puntuación total</span><span class="detail-value font-bold" style="font-size:1.4rem"><?= number_format($evaluation['TotalScore'], 2) ?></span></div>
    </div>

    <div style="margin-top:1.5rem">
      <?php
      $scoreItems = [
          'InnovationScore'   => 'Innovación',
          'TechnicalScore'    => 'Técnica',
          'PresentationScore' => 'Presentación',
          'UsabilityScore'    => 'Usabilidad',
      ];
      foreach ($scoreItems as $key => $label):
        $score = (float) $evaluation[$key];
        $pct   = ($score / 10) * 100;
      ?>
      <div style="margin-bottom:.75rem">
        <div style="display:flex;justify-content:space-between;margin-bottom:.2rem">
          <span class="text-sm"><?= $label ?></span>
          <span class="text-sm font-bold"><?= number_format($score, 1) ?>/10</span>
        </div>
        <div class="score-bar-track" style="height:8px">
          <div class="score-bar-fill" style="width:<?= $pct ?>%"></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if ($evaluation['Comments']): ?>
    <div style="margin-top:1.25rem">
      <span class="detail-label">Comentarios</span>
      <p style="margin-top:.4rem;font-size:.88rem"><?= nl2br(htmlspecialchars($evaluation['Comments'], ENT_QUOTES, 'UTF-8')) ?></p>
    </div>
    <?php endif; ?>
  </div>
</div>
