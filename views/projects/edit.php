<div class="page-header">
  <div>
    <h2>Editar proyecto</h2>
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>/?module=projects">Proyectos</a> &rsaquo;
      <?= htmlspecialchars($project['ProjectName'], ENT_QUOTES, 'UTF-8') ?> &rsaquo; Editar
    </div>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header"><h3>Modificar proyecto</h3></div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/?module=projects&action=update&id=<?= $project['ProjectID'] ?>">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="_method" value="PUT">
      <div class="form-grid">
        <div class="form-group">
          <label>Equipo</label>
          <input type="text" value="<?= htmlspecialchars($project['TeamName'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled>
        </div>
        <div class="form-group">
          <label for="project_name">Nombre del proyecto *</label>
          <input type="text" id="project_name" name="project_name" maxlength="200" required
                 value="<?= htmlspecialchars($project['ProjectName'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="category">Categoría *</label>
          <select id="category" name="category" required>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>"
              <?= $project['Category'] === $cat ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="status">Estado *</label>
          <select id="status" name="status" required>
            <?php foreach ($statuses as $status): ?>
            <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>"
              <?= $project['Status'] === $status ? 'selected' : '' ?>>
              <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group full">
          <label for="technology_stack">Stack tecnológico</label>
          <input type="text" id="technology_stack" name="technology_stack"
                 value="<?= htmlspecialchars($project['TechnologyStack'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group full">
          <label for="github_url">URL de GitHub</label>
          <input type="url" id="github_url" name="github_url"
                 value="<?= htmlspecialchars($project['GitHubURL'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group full">
          <label for="description">Descripción</label>
          <textarea id="description" name="description" rows="4"><?= htmlspecialchars($project['Description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="<?= BASE_URL ?>/?module=projects&action=show&id=<?= $project['ProjectID'] ?>" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
