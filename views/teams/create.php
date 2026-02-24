<div class="page-header">
  <div>
    <h2>Nuevo equipo</h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=teams">Equipos</a> &rsaquo; Crear</div>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header"><h3>Datos del equipo</h3></div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/?module=teams&action=store">
      <div class="form-grid">
        <div class="form-group">
          <label for="team_name">Nombre del equipo *</label>
          <input type="text" id="team_name" name="team_name" maxlength="100" required
                 placeholder="Ej: Code Wizards">
        </div>
        <div class="form-group">
          <label for="max_members">Máximo de miembros *</label>
          <input type="number" id="max_members" name="max_members" value="5" min="2" max="20" required>
        </div>
        <div class="form-group full">
          <label for="leader_id">Líder del equipo *</label>
          <select id="leader_id" name="leader_id" required>
            <option value="">— Selecciona el líder —</option>
            <?php foreach ($participants as $participant): ?>
            <option value="<?= $participant['ParticipantID'] ?>">
              <?= htmlspecialchars($participant['fullName'], ENT_QUOTES, 'UTF-8') ?>
              &lt;<?= htmlspecialchars($participant['Email'], ENT_QUOTES, 'UTF-8') ?>&gt;
            </option>
            <?php endforeach; ?>
          </select>
          <span class="form-hint">El líder será automáticamente añadido como miembro.</span>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Crear equipo</button>
        <a href="<?= BASE_URL ?>/?module=teams" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
