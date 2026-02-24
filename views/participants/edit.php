<div class="page-header">
  <div>
    <h2>Editar participante</h2>
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>/?module=participants">Participantes</a> &rsaquo;
      <?= htmlspecialchars($participant['FirstName'] . ' ' . $participant['LastName'], ENT_QUOTES, 'UTF-8') ?>
      &rsaquo; Editar
    </div>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header"><h3>Modificar datos</h3></div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/?module=participants&action=update&id=<?= $participant['ParticipantID'] ?>">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="_method" value="PUT">
      <div class="form-grid">
        <div class="form-group">
          <label for="first_name">Nombre *</label>
          <input type="text" id="first_name" name="first_name" maxlength="100" required
                 value="<?= htmlspecialchars($participant['FirstName'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="last_name">Apellidos *</label>
          <input type="text" id="last_name" name="last_name" maxlength="100" required
                 value="<?= htmlspecialchars($participant['LastName'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" id="email" name="email" maxlength="150" required
                 value="<?= htmlspecialchars($participant['Email'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="phone">Teléfono</label>
          <input type="tel" id="phone" name="phone" maxlength="20"
                 value="<?= htmlspecialchars($participant['Phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="university">Universidad *</label>
          <input type="text" id="university" name="university" maxlength="200" required
                 value="<?= htmlspecialchars($participant['University'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="major">Carrera *</label>
          <input type="text" id="major" name="major" maxlength="100" required
                 value="<?= htmlspecialchars($participant['Major'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="year_of_study">Año de estudio *</label>
          <input type="number" id="year_of_study" name="year_of_study" min="1" max="6" required
                 value="<?= $participant['YearOfStudy'] ?>">
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="<?= BASE_URL ?>/?module=participants&action=show&id=<?= $participant['ParticipantID'] ?>" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
