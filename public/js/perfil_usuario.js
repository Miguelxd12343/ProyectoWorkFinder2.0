window.addEventListener("pageshow", function (event) {
      if (event.persisted || (performance.getEntriesByType("navigation")[0]?.type === "back_forward")) {
        window.location.reload();
      }
    });

    // Auto-hide mensaje después de 5 segundos
    document.addEventListener('DOMContentLoaded', function() {
      const mensaje = document.querySelector('.mensaje');
      if (mensaje && mensaje.classList.contains('success')) {
        setTimeout(() => {
          mensaje.style.opacity = '0';
          mensaje.style.transform = 'translateY(-10px)';
          setTimeout(() => mensaje.remove(), 300);
        }, 5000);
      }
    });

    document.getElementById('foto-input').addEventListener('change', function() {
    if (this.files && this.files[0]) {
        // Auto-submit el formulario cuando se selecciona una foto
        document.querySelector('form').submit();
    }
});