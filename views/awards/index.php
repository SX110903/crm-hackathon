<?php $module = 'awards'; ?>
<div class="page-header">
  <div>
    <h2>Premios</h2>
    <div class="breadcrumb">Gestión y asignación de premios</div>
  </div>
  <a href="<?= BASE_URL ?>/?module=awards&action=create" class="btn btn-primary">+ Nuevo premio</a>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Premio</th><th>Categoría</th><th>Dotación</th>
          <th>Proyecto asignado</th><th>Equipo</th><th>Fecha entrega</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($awards)): ?>
        <tr><td colspan="8"><div class="empty-state"><div class="empty-icon">★</div><p>No hay premios registrados.</p></div></td></tr>
        <?php else: ?>
        <?php foreach ($awards as $award): ?>
        <tr>
          <td class="text-muted text-sm">#<?= $award['AwardID'] ?></td>
          <td class="font-bold"><?= htmlspecialchars($award['AwardName'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><span class="badge badge-yellow"><?= htmlspecialchars($award['Category'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></td>
          <td class="text-sm"><?= htmlspecialchars($award['Prize'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td>
            <?php if ($award['ProjectName']): ?>
              <span class="badge badge-green"><?= htmlspecialchars($award['ProjectName'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php else: ?>
              <span class="badge badge-gray">Sin asignar</span>
            <?php endif; ?>
          </td>
          <td class="text-sm text-muted"><?= htmlspecialchars($award['TeamName'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-sm text-muted"><?= htmlspecialchars($award['AwardedDate'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td>
            <div class="btn-group">
              <?php if (!$award['ProjectName']): ?>
              <a href="<?= BASE_URL ?>/?module=awards&action=assign&id=<?= $award['AwardID'] ?>" class="btn btn-success btn-xs">Asignar</a>
              <?php else: ?>
              <form method="POST" action="<?= BASE_URL ?>/?module=awards&action=delete&id=<?= $award['AwardID'] ?>" class="form-delete" style="display:inline">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn btn-warning btn-xs">Quitar asignación</button>
              </form>
              <?php endif; ?>
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
