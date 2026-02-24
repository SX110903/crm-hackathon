<div class="page-header">
  <div>
    <h2>Nuevo proyecto</h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=projects">Proyectos</a> &rsaquo; Crear</div>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header"><h3>Datos del proyecto</h3></div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/?module=projects&action=store">
      <div class="form-grid">
        <div class="form-group">
          <label for="team_id">Equipo *</label>
          <select id="team_id" name="team_id" required>
            <option value="">— Selecciona el equipo —</option>
            <?php foreach ($teams as $team): ?>
            <option value="<?= $team['TeamID'] ?>"><?= htmlspecialchars($team['TeamName'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="project_name">Nombre del proyecto *</label>
          <input type="text" id="project_name" name="project_name" maxlength="200" required placeholder="Ej: EcoTracker">
        </div>
        <div class="form-group">
          <label for="category">Categoría *</label>
          <select id="category" name="category" required>
            <option value="">— Categoría —</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="status">Estado *</label>
          <select id="status" name="status" required>
            <?php foreach ($statuses as $status): ?>
            <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group full">
          <label for="technology_stack">Stack tecnológico</label>
          <input type="text" id="technology_stack" name="technology_stack" placeholder="Ej: React, Node.js, MongoDB">
        </div>
        <div class="form-group full">
          <label for="github_url">URL de GitHub</label>
          <input type="url" id="github_url" name="github_url" placeholder="https://github.com/...">
        </div>
        <div class="form-group full">
          <label for="description">Descripción</label>
          <textarea id="description" name="description" rows="4" placeholder="Describe brevemente el proyecto..."></textarea>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Crear proyecto</button>
        <a href="<?= BASE_URL ?>/?module=projects" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
