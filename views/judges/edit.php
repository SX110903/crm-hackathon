<div class="page-header">
  <div>
    <h2>Editar juez</h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=judges">Jueces</a> &rsaquo; Editar</div>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header"><h3>Modificar datos</h3></div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/?module=judges&action=update&id=<?= $judge['JudgeID'] ?>">
      <input type="hidden" name="_method" value="PUT">
      <div class="form-grid">
        <div class="form-group">
          <label for="first_name">Nombre *</label>
          <input type="text" id="first_name" name="first_name" maxlength="100" required value="<?= htmlspecialchars($judge['FirstName'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="last_name">Apellidos *</label>
          <input type="text" id="last_name" name="last_name" maxlength="100" required value="<?= htmlspecialchars($judge['LastName'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" id="email" name="email" maxlength="150" required value="<?= htmlspecialchars($judge['Email'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="company">Empresa</label>
          <input type="text" id="company" name="company" maxlength="150" value="<?= htmlspecialchars($judge['Company'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="expertise">Área de expertise *</label>
          <input type="text" id="expertise" name="expertise" maxlength="100" required value="<?= htmlspecialchars($judge['Expertise'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="years_of_experience">Años de experiencia</label>
          <input type="number" id="years_of_experience" name="years_of_experience" min="0" max="60" value="<?= $judge['YearsOfExperience'] ?>">
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="<?= BASE_URL ?>/?module=judges&action=show&id=<?= $judge['JudgeID'] ?>" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
