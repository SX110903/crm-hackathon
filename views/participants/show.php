<div class="page-header">
  <div>
    <h2><?= htmlspecialchars($participant['FirstName'] . ' ' . $participant['LastName'], ENT_QUOTES, 'UTF-8') ?></h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=participants">Participantes</a> &rsaquo; Detalle</div>
  </div>
  <div class="btn-group">
    <a href="<?= BASE_URL ?>/?module=participants&action=edit&id=<?= $participant['ParticipantID'] ?>" class="btn btn-secondary">Editar</a>
    <a href="<?= BASE_URL ?>/?module=participants" class="btn btn-secondary">← Volver</a>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header"><h3>Datos personales</h3></div>
  <div class="card-body">
    <div class="detail-grid">
      <div class="detail-item">
        <span class="detail-label">Nombre completo</span>
        <span class="detail-value"><?= htmlspecialchars($participant['FirstName'] . ' ' . $participant['LastName'], ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Email</span>
        <span class="detail-value"><?= htmlspecialchars($participant['Email'], ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Teléfono</span>
        <span class="detail-value"><?= htmlspecialchars($participant['Phone'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Universidad</span>
        <span class="detail-value"><?= htmlspecialchars($participant['University'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Carrera</span>
        <span class="detail-value"><?= htmlspecialchars($participant['Major'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Año de estudio</span>
        <span class="detail-value"><?= $participant['YearOfStudy'] ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Equipo</span>
        <span class="detail-value">
          <?php if ($participant['TeamName']): ?>
            <a href="<?= BASE_URL ?>/?module=teams&action=show&id=<?= $participant['TeamID'] ?>">
              <?= htmlspecialchars($participant['TeamName'], ENT_QUOTES, 'UTF-8') ?>
            </a>
            <span class="badge badge-blue" style="margin-left:.3rem"><?= htmlspecialchars($participant['teamRole'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
          <?php else: ?>
            <span class="text-muted">Sin equipo</span>
          <?php endif; ?>
        </span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Registrado</span>
        <span class="detail-value text-muted"><?= htmlspecialchars(substr($participant['RegistrationDate'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></span>
      </div>
    </div>
  </div>
</div>
