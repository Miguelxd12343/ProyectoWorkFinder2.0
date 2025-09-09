document.addEventListener('DOMContentLoaded', function() {
  // Crear botón toggle para móvil si no existe
  if (!document.querySelector('.sidebar-toggle')) {
    const toggleButton = document.createElement('button');
    toggleButton.className = 'sidebar-toggle';
    toggleButton.innerHTML = '☰';
    toggleButton.setAttribute('aria-label', 'Abrir menú');
    document.body.appendChild(toggleButton);
    
    // Crear overlay
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
    
    const sidebar = document.querySelector('.sidebar');
    
    // Función para abrir sidebar
    function openSidebar() {
      sidebar.classList.add('active');
      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
    
    // Función para cerrar sidebar
    function closeSidebar() {
      sidebar.classList.remove('active');
      overlay.classList.remove('active');
      document.body.style.overflow = '';
    }
    
    // Event listeners
    toggleButton.addEventListener('click', openSidebar);
    overlay.addEventListener('click', closeSidebar);
    
    // Cerrar con ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && sidebar.classList.contains('active')) {
        closeSidebar();
      }
    });
    
    // Cerrar al hacer clic en un enlace (en móvil)
    const sidebarLinks = sidebar.querySelectorAll('a');
    sidebarLinks.forEach(link => {
      link.addEventListener('click', function() {
        if (window.innerWidth <= 968) {
          closeSidebar();
        }
      });
    });
  }
  
  // Efecto de hover mejorado para los elementos del menú
  const menuItems = document.querySelectorAll('.sidebar li a');
  menuItems.forEach(item => {
    item.addEventListener('mouseenter', function() {
      this.style.textShadow = '0 0 10px rgba(255, 255, 255, 0.5)';
    });
    
    item.addEventListener('mouseleave', function() {
      this.style.textShadow = 'none';
    });
  });

  // Animación de entrada para las tarjetas
  const cards = document.querySelectorAll('.stat-card, .quick-actions, .recent-activity');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  });
  
  cards.forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    card.style.transition = 'all 0.6s ease';
    observer.observe(card);
  });
});
