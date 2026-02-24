<?php
/**
 * Partial de paginación. realmnte esto lo que aplico es POO
 * Requiere: $pagination (array), $module (string)
 * Opcional: $extraParams (array) para parámetros adicionales en la URL
 */
if (!isset($pagination) || $pagination['totalPages'] <= 1) return;

$currentPage = $pagination['currentPage'];
$totalPages  = $pagination['totalPages'];
$extra       = '';
if (!empty($extraParams)) {
    foreach ($extraParams as $k => $v) {
        $extra .= '&' . urlencode($k) . '=' . urlencode($v);
    }
}
//Esto realmente es como que direccionamos los archivos
function paginationUrl(string $module, int $page, string $extra): string {
    return BASE_URL . "/?module={$module}&page={$page}{$extra}";
}
?>
<nav class="pagination">
  <?php if ($currentPage > 1): ?>
    <a href="<?= paginationUrl($module, 1, $extra) ?>">&laquo;</a>
    <a href="<?= paginationUrl($module, $currentPage - 1, $extra) ?>">&lsaquo;</a>
  <?php else: ?>
    <span class="disabled">&laquo;</span>
    <span class="disabled">&lsaquo;</span>
  <?php endif; ?>

  <?php
  $range = 2;
  $start = max(1, $currentPage - $range);
  $end   = min($totalPages, $currentPage + $range);
  for ($p = $start; $p <= $end; $p++):
  ?>
    <?php if ($p === $currentPage): ?>
      <span class="current"><?= $p ?></span>
    <?php else: ?>
      <a href="<?= paginationUrl($module, $p, $extra) ?>"><?= $p ?></a>
    <?php endif; ?>
  <?php endfor; ?>

  <?php if ($currentPage < $totalPages): ?>
    <a href="<?= paginationUrl($module, $currentPage + 1, $extra) ?>">&rsaquo;</a>
    <a href="<?= paginationUrl($module, $totalPages, $extra) ?>">&raquo;</a>
  <?php else: ?>
    <span class="disabled">&rsaquo;</span>
    <span class="disabled">&raquo;</span>
  <?php endif; ?>

  <span class="text-muted text-sm" style="margin-left:.5rem">
    <?= number_format($pagination['totalRecords']) ?> registros &mdash; pág. <?= $currentPage ?>/<?= $totalPages ?>
  </span>
</nav>
