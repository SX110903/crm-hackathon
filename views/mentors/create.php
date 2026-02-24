<div class="page-header">
  <div>
    <h2>Nuevo mentor</h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=mentors">Mentores</a> &rsaquo; Crear</div>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header"><h3>Datos del mentor</h3></div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/?module=mentors&action=store">
      <div class="form-grid">
        <div class="form-group">
          <label for="first_name">Nombre *</label>
          <input type="text" id="first_name" name="first_name" maxlength="100" required>
        </div>
        <div class="form-group">
          <label for="last_name">Apellidos *</label>
          <input type="text" id="last_name" name="last_name" maxlength="100" required>
        </div>
        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" id="email" name="email" maxlength="150" required>
        </div>
        <div class="form-group">
          <label for="company">Empresa</label>
          <input type="text" id="company" name="company" maxlength="150" placeholder="Ej: TechCorp">
        </div>
        <div class="form-group">
          <label for="specialization">Especialización *</label>
          <input type="text" id="specialization" name="specialization" maxlength="100" required placeholder="Ej: Machine Learning">
        </div>
        <div class="form-group">
          <label for="available_slots">Slots disponibles</label>
          <input type="number" id="available_slots" name="available_slots" value="3" min="0" max="50">
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Registrar mentor</button>
        <a href="<?= BASE_URL ?>/?module=mentors" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
