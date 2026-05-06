<div class="page-header">
  <div>
    <h2>Nueva evaluación</h2>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>/?module=evaluations">Evaluaciones</a> &rsaquo; Crear</div>
  </div>
</div>

<?php include ROOT_PATH . '/views/layout/_flash.php'; ?>

<div class="card">
  <div class="card-header"><h3>Formulario de evaluación</h3></div>
  <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/?module=evaluations&action=store">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <div class="form-grid">
        <div class="form-group">
          <label for="project_id">Proyecto *</label>
          <select id="project_id" name="project_id" required>
            <option value="">— Selecciona el proyecto —</option>
            <?php foreach ($projects as $project): ?>
            <option value="<?= $project['ProjectID'] ?>"
              <?= (isset($preselectedProjectId) && $preselectedProjectId === (int) $project['ProjectID']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($project['displayName'], ENT_QUOTES, 'UTF-8') ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="judge_id">Juez *</label>
          <select id="judge_id" name="judge_id" required>
            <option value="">— Selecciona el juez —</option>
            <?php foreach ($judges as $judge): ?>
            <option value="<?= $judge['JudgeID'] ?>">
              <?= htmlspecialchars($judge['fullName'], ENT_QUOTES, 'UTF-8') ?>
              — <?= htmlspecialchars($judge['Expertise'], ENT_QUOTES, 'UTF-8') ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Puntuaciones con slider -->
        <?php
        $scores = [
            'innovation_score'   => ['Innovación',   'innovation_score'],
            'technical_score'    => ['Técnica',       'technical_score'],
            'presentation_score' => ['Presentación',  'presentation_score'],
            'usability_score'    => ['Usabilidad',    'usability_score'],
        ];
        foreach ($scores as $name => [$label, $inputId]):
        ?>
        <div class="form-group">
          <label for="<?= $inputId ?>">
            <?= $label ?> (0 – 10) *
          </label>
          <div class="score-range">
            <input type="range" id="<?= $inputId ?>" name="<?= $name ?>"
                   min="0" max="10" step="0.5" value="5" required>
            <span class="score-display" id="<?= $inputId ?>_display">5.0</span>
          </div>
        </div>
        <?php endforeach; ?>

        <div class="form-group full">
          <label for="comments">Comentarios</label>
          <textarea id="comments" name="comments" rows="4"
                    placeholder="Observaciones del juez sobre el proyecto..."></textarea>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Registrar evaluación</button>
        <a href="<?= BASE_URL ?>/?module=evaluations" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
