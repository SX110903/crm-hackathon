<div class="page-header">
  <div>
    <h2>Editar equipo</h2>
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>/?module=teams">Equipos</a> &rsaquo;
      <a href="<?= BASE_URL ?>/?module=teams&action=show&id=<?= $team['TeamID'] ?>"><?= htmlspecialchars($team['TeamName'], ENT_QUOTES, 'UTF-8') ?></a>
      &rsaquo; Editar
    </div>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header"><h3>Modificar datos</h3></div>
  <div class="card-body">
    <!-- POST + hidden _method=PUT para simular PUT en HTML forms -->
    <form method="POST" action="<?= BASE_URL ?>/?module=teams&action=update&id=<?= $team['TeamID'] ?>">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="_method" value="PUT">
      <div class="form-grid">
        <div class="form-group">
          <label for="team_name">Nombre del equipo *</label>
          <input type="text" id="team_name" name="team_name" maxlength="100" required
                 value="<?= htmlspecialchars($team['TeamName'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="form-group">
          <label for="max_members">Máximo de miembros *</label>
          <input type="number" id="max_members" name="max_members" min="2" max="20" required
                 value="<?= $team['MaxMembers'] ?>">
        </div>
        <div class="form-group">
          <label>Líder actual</label>
          <input type="text" value="<?= htmlspecialchars($team['leaderName'] ?? '—', ENT_QUOTES, 'UTF-8') ?>" disabled>
          <span class="form-hint">El líder no puede cambiarse desde aquí.</span>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="<?= BASE_URL ?>/?module=teams&action=show&id=<?= $team['TeamID'] ?>" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
