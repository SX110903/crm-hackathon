<script>
// Actualizar valor mostrado de los sliders de puntuación
document.querySelectorAll('input[type="range"]').forEach(function(range) {
  var display = document.getElementById(range.id + '_display');
  if (display) {
    display.textContent = parseFloat(range.value).toFixed(1);
    range.addEventListener('input', function() {
      display.textContent = parseFloat(this.value).toFixed(1);
    });
  }
});

// Confirmación de eliminación
document.querySelectorAll('form.form-delete').forEach(function(form) {
  form.addEventListener('submit', function(e) {
    if (!confirm('¿Confirmar eliminación? Esta acción no se puede deshacer.')) {
      e.preventDefault();
    }
  });
});
</script>
</body>
</html>
