<?php $module = 'teams'; ?>
<div class="page-header">
  <div>
    <h2>Equipos</h2>
    <div class="breadcrumb">Gestión de equipos participantes</div>
  </div>
  <a href="<?= BASE_URL ?>/?module=teams&action=create" class="btn btn-primary">+ Nuevo equipo</a>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<!-- ─── Buscador ─────────────────────────────────────────────────────────── -->
<div class="card" style="margin-bottom:1rem;">
  <div class="card-body" style="padding:.75rem 1rem;">
    <form method="GET"
          action="<?= BASE_URL ?>/"
          class="search-form"
          style="display:flex;gap:.75rem;align-items:center;">
      <input type="hidden" name="module" value="teams">
      <input type="hidden" name="action" value="index">
      <input type="search"
             name="search"
             value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>"
             placeholder="Buscar equipo o líder…"
             style="flex:1;padding:.5rem .75rem;border:1px solid var(--color-border);
                    border-radius:var(--radius-sm);font-size:.87rem;outline:none;"
             autocomplete="off">
      <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
      <?php if (!empty($search)): ?>
      <a href="<?= BASE_URL ?>/?module=teams" class="btn btn-secondary btn-sm">Limpiar</a>
      <?php endif; ?>
    </form>
    <?php if (!empty($search)): ?>
    <p style="margin-top:.5rem;font-size:.8rem;color:var(--color-text-muted);">
      Resultados para: <strong><?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?></strong>
      (<?= $pagination['totalRecords'] ?> encontrados)
    </p>
    <?php endif; ?>
  </div>
</div>

<!-- ─── Tabla ─────────────────────────────────────────────────────────────── -->
<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Líder</th>
          <th>Miembros</th>
          <th>Proyectos</th>
          <th>Puntuación</th>
          <th>Creado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($teams)): ?>
        <tr>
          <td colspan="8">
            <div class="empty-state">
              <div class="empty-icon">◎</div>
              <p><?= !empty($search) ? 'Sin resultados para la búsqueda.' : 'No hay equipos registrados.' ?></p>
            </div>
          </td>
        </tr>
        <?php else: ?>
        <?php foreach ($teams as $team): ?>
        <tr>
          <td class="text-muted text-sm">#<?= $team['TeamID'] ?></td>
          <td>
            <a href="<?= BASE_URL ?>/?module=teams&action=show&id=<?= $team['TeamID'] ?>">
              <?= htmlspecialchars($team['TeamName'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </td>
          <td class="text-sm"><?= htmlspecialchars($team['leaderName'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-center"><?= (int)($team['TotalMembers'] ?? 0) ?>/<?= $team['MaxMembers'] ?></td>
          <td class="text-center"><?= $team['projectCount'] ?></td>
          <td class="font-bold"><?= $team['avgScore'] > 0 ? number_format((float)$team['avgScore'], 1) : '—' ?></td>
          <td class="text-sm text-muted"><?= htmlspecialchars(substr($team['CreatedDate'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
          <td>
            <div class="btn-group">
              <a href="<?= BASE_URL ?>/?module=teams&action=show&id=<?= $team['TeamID'] ?>"
                 class="btn btn-secondary btn-xs">Ver</a>
              <a href="<?= BASE_URL ?>/?module=teams&action=edit&id=<?= $team['TeamID'] ?>"
                 class="btn btn-secondary btn-xs">Editar</a>
              <form method="POST"
                    action="<?= BASE_URL ?>/?module=teams&action=delete&id=<?= $team['TeamID'] ?>"
                    class="form-delete"
                    style="display:inline">
                <input type="hidden" name="_csrf_token"
                       value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn btn-danger btn-xs">Eliminar</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if (!empty($pagination)): ?>
  <div class="card-footer"><?php include ROOT_PATH . '/views/layout/_pagination.php'; ?></div>
  <?php endif; ?>
</div>
