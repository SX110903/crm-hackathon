<div class="page-header">
  <div>
    <h2>Nuevo premio</h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=awards">Premios</a> &rsaquo; Crear</div>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header"><h3>Datos del premio</h3></div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/?module=awards&action=store">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <div class="form-grid">
        <div class="form-group">
          <label for="award_name">Nombre del premio *</label>
          <input type="text" id="award_name" name="award_name" maxlength="150" required
                 placeholder="Ej: Primer Lugar">
        </div>
        <div class="form-group">
          <label for="category">Categoría *</label>
          <input type="text" id="category" name="category" maxlength="100" required
                 placeholder="Ej: General, Innovación, Diseño">
        </div>
        <div class="form-group">
          <label for="prize">Dotación</label>
          <input type="text" id="prize" name="prize" maxlength="200"
                 placeholder="Ej: 5000€ + Incubación">
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Crear premio</button>
        <a href="<?= BASE_URL ?>/?module=awards" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
