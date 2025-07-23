<?php
require 'auth_guard.php'; // Aquí ya está session_start() y headers de no caché
require_once(__DIR__ . '/conexion.php');

// Obtener estadísticas del usuario
$userId = $_SESSION['usuario_id'];
$userName = $_SESSION['usuario_nombre'];

// Consultas para estadísticas
try {
    // Ofertas activas
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM puestodetrabajo WHERE Estado = 'Activa'");
    $stmt->execute();
    $ofertasActivas = $stmt->fetchColumn();
    
    // Postulaciones del usuario
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM solicitudempleo WHERE IdUsuario = ?");
    $stmt->execute([$userId]);
    $misPostulaciones = $stmt->fetchColumn();
    
    // Postulaciones recientes (últimas 5)
    $stmt = $pdo->prepare("
        SELECT s.*, p.Titulo, p.Empresa, s.FechaEnvio as FechaSolicitud 
        FROM solicitudempleo s 
        JOIN puestodetrabajo p ON s.IdPuestoTrabajo = p.IdPuesto 
        WHERE s.IdUsuario = ? 
        ORDER BY s.FechaEnvio DESC 
        LIMIT 5
    ");
    $stmt->execute([$userId]);
    $postulacionesRecientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Invitaciones pendientes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM invitaciones WHERE IdCandidato = ?");
    $stmt->execute([$userId]);
    $invitacionesPendientes = $stmt->fetchColumn();
    
} catch (PDOException $e) {
    $ofertasActivas = 0;
    $misPostulaciones = 0;
    $postulacionesRecientes = [];
    $invitacionesPendientes = 0;
}

// Función para obtener tiempo relativo
function tiempoRelativo($fecha) {
    $tiempo = time() - strtotime($fecha);
    
    if ($tiempo < 60) return 'Hace unos segundos';
    if ($tiempo < 3600) return 'Hace ' . floor($tiempo/60) . ' minutos';
    if ($tiempo < 86400) return 'Hace ' . floor($tiempo/3600) . ' horas';
    if ($tiempo < 2592000) return 'Hace ' . floor($tiempo/86400) . ' días';
    
    return date('d/m/Y', strtotime($fecha));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard - WorkFinderPro</title>
  <link rel="stylesheet" href="../CSS/dashboard_usuario.css" />

  <!-- Evitar que Chrome muestre la página desde bfcache al volver atrás -->
  <script>
    window.addEventListener("pageshow", function (event) {
      if (event.persisted || (performance.getEntriesByType("navigation")[0]?.type === "back_forward")) {
        window.location.reload();
      }
    });
  </script>
</head>
<body>
<div class="sidebar">
  <h2><a href="../index.html" class="logo-link">WorkFinderPro</a></h2>
  <ul>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard_usuario.php' ? 'active' : '' ?>">
      <a href="dashboard_usuario.php">Inicio</a>
    </li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'perfil.php' ? 'active' : '' ?>">
      <a href="perfil.php">Perfil</a>
    </li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'ver_ofertas.php' ? 'active' : '' ?>">
      <a href="ver_ofertas.php">Ofertas</a>
    </li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'invitaciones.php' ? 'active' : '' ?>">
      <a href="invitaciones.php">Invitaciones✉️</a>
    </li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'solicitudes.php' ? 'active' : '' ?>">
      <a href="solicitudes.php">Solicitudes</a>
    </li>
    <li><a href="logout.php">Cerrar sesión</a></li>
  </ul>
</div>

<div class="main">
  <div class="dashboard-container">
    <div class="header">
      <?php if (isset($_GET['msg'])): ?>
        <div class="alert">
          <?= htmlspecialchars($_GET['msg']) ?>
        </div>
      <?php endif; ?>

      <h1>Bienvenido, <strong><?= htmlspecialchars($userName) ?></strong></h1>
      <p class="subtitle">Tu espacio personal para gestionar oportunidades laborales</p>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="stats-grid">
      <div class="stat-card ofertas">
        <div class="icon">💼</div>
        <h3>Ofertas Disponibles</h3>
        <div class="number"><?= $ofertasActivas ?></div>
        <p class="description">Ofertas de trabajo activas en la plataforma</p>
      </div>

      <div class="stat-card postulaciones">
        <div class="icon">📝</div>
        <h3>Mis Postulaciones</h3>
        <div class="number"><?= $misPostulaciones ?></div>
        <p class="description">Total de postulaciones que has realizado</p>
      </div>

      <div class="stat-card invitaciones">
        <div class="icon">✉️</div>
        <h3>Invitaciones</h3>
        <div class="number"><?= $invitacionesPendientes ?></div>
        <p class="description">Invitaciones de empresas para ti</p>
      </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="quick-actions">
      <h2>🚀 Acciones Rápidas</h2>
      <div class="actions-grid">
        <a href="ver_ofertas.php" class="action-button">
          <div class="icon">🔍</div>
          <div class="label">Buscar Ofertas</div>
        </a>
        
        <a href="perfil.php" class="action-button">
          <div class="icon">👤</div>
          <div class="label">Mi Perfil</div>
        </a>
        
        <a href="invitaciones.php" class="action-button">
          <div class="icon">✉️</div>
          <div class="label">Mis Invitaciones</div>
        </a>
        
        <a href="solicitudes.php" class="action-button">
          <div class="icon">📋</div>
          <div class="label">Mis Solicitudes</div>
        </a>
      </div>
    </div>

    <!-- Actividad reciente -->
    <?php if (!empty($postulacionesRecientes)): ?>
    <div class="recent-activity">
      <h2>📈 Actividad Reciente</h2>
      <?php foreach ($postulacionesRecientes as $postulacion): ?>
        <div class="activity-item">
          <div class="activity-icon">📝</div>
          <div class="activity-content">
            <div class="activity-title">
              Postulación a <?= htmlspecialchars($postulacion['Titulo']) ?>
            </div>
            <div class="activity-time">
              <?= isset($postulacion['Empresa']) ? htmlspecialchars($postulacion['Empresa']) . ' • ' : '' ?><?= tiempoRelativo($postulacion['FechaSolicitud']) ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="recent-activity">
      <h2>📈 Actividad Reciente</h2>
      <div class="activity-item">
        <div class="activity-icon">💡</div>
        <div class="activity-content">
          <div class="activity-title">¡Comienza tu búsqueda!</div>
          <div class="activity-time">Explora las ofertas disponibles y postúlate a las que más te interesen</div>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
// JavaScript para el sidebar responsivo
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
</script>
</body>
</html>