<div class="page-header">
  <div>
    <h2>Asignar premio</h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=awards">Premios</a> &rsaquo; Asignar</div>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header">
    <h3>Premio: <span class="badge badge-yellow"><?= htmlspecialchars($award['AwardName'], ENT_QUOTES, 'UTF-8') ?></span></h3>
  </div>
  <div class="card-body">
    <div class="detail-grid" style="margin-bottom:1.5rem">
      <div class="detail-item">
        <span class="detail-label">Categoría</span>
        <span class="detail-value"><?= htmlspecialchars($award['Category'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Dotación</span>
        <span class="detail-value font-bold"><?= htmlspecialchars($award['Prize'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
    </div>

    <!-- POST + _method=PUT para asignación (es una actualización) -->
    <form method="POST" action="<?= BASE_URL ?>/?module=awards&action=update&id=<?= $award['AwardID'] ?>">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="_method" value="PUT">
      <div class="form-grid">
        <div class="form-group">
          <label for="project_id">Proyecto ganador *</label>
          <select id="project_id" name="project_id" required>
            <option value="">— Selecciona el proyecto —</option>
            <?php foreach ($projects as $project): ?>
            <option value="<?= $project['ProjectID'] ?>">
              <?= htmlspecialchars($project['displayName'], ENT_QUOTES, 'UTF-8') ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="awarded_date">Fecha de entrega *</label>
          <input type="date" id="awarded_date" name="awarded_date"
                 value="<?= date('Y-m-d') ?>" required>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-success">Asignar premio</button>
        <a href="<?= BASE_URL ?>/?module=awards" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
