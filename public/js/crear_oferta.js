        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }

        // Cerrar sidebar al cambiar tamaño de ventana
        window.addEventListener('resize', function() {
            if (window.innerWidth > 968) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.querySelector('.sidebar-overlay');
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        });
  