<div class="page-header">
  <div>
    <h2>Editar mentor</h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=mentors">Mentores</a> &rsaquo; Editar</div>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header"><h3>Modificar datos</h3></div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/?module=mentors&action=update&id=<?= $mentor['MentorID'] ?>">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="_method" value="PUT">
      <div class="form-grid">
        <div class="form-group">
          <label for="first_name">Nombre *</label>
          <input type="text" id="first_name" name="first_name" maxlength="100" required value="<?= htmlspecialchars($mentor['FirstName'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="last_name">Apellidos *</label>
          <input type="text" id="last_name" name="last_name" maxlength="100" required value="<?= htmlspecialchars($mentor['LastName'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" id="email" name="email" maxlength="150" required value="<?= htmlspecialchars($mentor['Email'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="company">Empresa</label>
          <input type="text" id="company" name="company" maxlength="150" value="<?= htmlspecialchars($mentor['Company'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="specialization">Especialización *</label>
          <input type="text" id="specialization" name="specialization" maxlength="100" required value="<?= htmlspecialchars($mentor['Specialization'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="available_slots">Slots disponibles</label>
          <input type="number" id="available_slots" name="available_slots" min="0" max="50" value="<?= $mentor['AvailableSlots'] ?>">
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="<?= BASE_URL ?>/?module=mentors&action=show&id=<?= $mentor['MentorID'] ?>" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
