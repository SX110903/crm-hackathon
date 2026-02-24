<div class="page-header">
  <div>
    <h2>Nuevo participante</h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=participants">Participantes</a> &rsaquo; Crear</div>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header"><h3>Datos del participante</h3></div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/?module=participants&action=store">
      <div class="form-grid">
        <div class="form-group">
          <label for="first_name">Nombre *</label>
          <input type="text" id="first_name" name="first_name" maxlength="100" required placeholder="Ej: Carlos">
        </div>
        <div class="form-group">
          <label for="last_name">Apellidos *</label>
          <input type="text" id="last_name" name="last_name" maxlength="100" required placeholder="Ej: García López">
        </div>
        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" id="email" name="email" maxlength="150" required placeholder="carlos@universidad.edu">
        </div>
        <div class="form-group">
          <label for="phone">Teléfono</label>
          <input type="tel" id="phone" name="phone" maxlength="20" placeholder="+34612345678">
        </div>
        <div class="form-group">
          <label for="university">Universidad *</label>
          <input type="text" id="university" name="university" maxlength="200" required placeholder="Ej: Universidad Politécnica de Valencia">
        </div>
        <div class="form-group">
          <label for="major">Carrera *</label>
          <input type="text" id="major" name="major" maxlength="100" required placeholder="Ej: Ingeniería Informática">
        </div>
        <div class="form-group">
          <label for="year_of_study">Año de estudio *</label>
          <input type="number" id="year_of_study" name="year_of_study" value="1" min="1" max="6" required>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Registrar participante</button>
        <a href="<?= BASE_URL ?>/?module=participants" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
