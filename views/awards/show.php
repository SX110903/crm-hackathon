<div class="page-header">
  <div>
    <h2><?= htmlspecialchars($award['AwardName'], ENT_QUOTES, 'UTF-8') ?></h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=awards">Premios</a> &rsaquo; Detalle</div>
  </div>
  <div class="btn-group">
    <?php if (!$award['ProjectName']): ?>
    <a href="<?= BASE_URL ?>/?module=awards&action=assign&id=<?= $award['AwardID'] ?>" class="btn btn-success">Asignar proyecto</a>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>/?module=awards" class="btn btn-secondary">← Volver</a>
  </div>
</div>
    <!--Esto realmente añadimos los flashs-->
<?php include ROOT_PATH . '/views/layout/_flash.php'; ?> 

<div class="card">
  <div class="card-header"><h3>Detalle del premio</h3></div>
  <div class="card-body">
    <div class="detail-grid">
      <div class="detail-item">
        <span class="detail-label">Premio</span>
        <span class="detail-value font-bold" style="font-size:1.1rem"><?= htmlspecialchars($award['AwardName'], ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Categoría</span>
        <span class="detail-value"><span class="badge badge-yellow"><?= htmlspecialchars($award['Category'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Dotación</span>
        <span class="detail-value"><?= htmlspecialchars($award['Prize'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Estado</span>
        <span class="detail-value">
          <?= $award['ProjectName']
            ? '<span class="badge badge-green">Asignado</span>'
            : '<span class="badge badge-gray">Sin asignar</span>' ?>
        </span>
      </div>
      <?php if ($award['ProjectName']): ?>
      <div class="detail-item">
        <span class="detail-label">Proyecto ganador</span>
        <span class="detail-value"><?= htmlspecialchars($award['ProjectName'], ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Equipo</span>
        <span class="detail-value"><?= htmlspecialchars($award['TeamName'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Líder del equipo</span>
        <span class="detail-value"><?= htmlspecialchars($award['leaderName'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Fecha de entrega</span>
        <span class="detail-value"><?= htmlspecialchars($award['AwardedDate'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
