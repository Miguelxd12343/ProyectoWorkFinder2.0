<?php
require 'auth_guard.php';
require_once(__DIR__ . '/conexion.php');

// Refuerzo de control de caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header('Location: login.php');
    exit;
}

$empresaId = $_SESSION['usuario_id'];
$nombreEmpresa = $_SESSION['usuario_nombre'];

// Obtener estadísticas de la empresa
try {
    // Ofertas activas
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM puestodetrabajo WHERE IdUsuario = ? AND Estado = 'Activa'");
    $stmt->execute([$empresaId]);
    $ofertasActivas = $stmt->fetchColumn();
    
    // Total de solicitudes recibidas
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM solicitudempleo s 
        JOIN puestodetrabajo p ON s.IdPuestoTrabajo = p.IdPuesto 
        WHERE p.IdUsuario = ?
    ");
    $stmt->execute([$empresaId]);
    $totalSolicitudes = $stmt->fetchColumn();
    
    // Invitaciones enviadas
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM invitaciones WHERE IdEmpresa = ?");
    $stmt->execute([$empresaId]);
    $invitacionesEnviadas = $stmt->fetchColumn();
    
    // Candidatos disponibles
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE IdRol = 2");
    $stmt->execute();
    $candidatosDisponibles = $stmt->fetchColumn();
    
} catch (PDOException $e) {
    $ofertasActivas = 0;
    $totalSolicitudes = 0;
    $invitacionesEnviadas = 0;
    $candidatosDisponibles = 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Empresa - WorkFinderPro</title>
    <link rel="stylesheet" href="../CSS/dashboard_empresa.css">
    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        });
    </script>
</head>
<body>
    <div class="sidebar">
        <h2><a href="../index.html" class="logo-link">WorkFinderPro</a></h2>
        <ul>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard_empresa.php' ? 'active' : '' ?>">
                <a href="dashboard_empresa.php">Inicio</a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'crear_oferta.php' ? 'active' : '' ?>">
                <a href="crear_oferta.php">Crear Oferta</a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'ver_ofertas_empresa.php' ? 'active' : '' ?>">
                <a href="ver_ofertas_empresa.php">Ver Ofertas Publicadas</a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'invitar_candidatos.php' ? 'active' : '' ?>">
                <a href="invitar_candidatos.php">Invitar Candidatos</a>
            </li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
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

                <h1>Bienvenido, <strong><?= htmlspecialchars($nombreEmpresa) ?></strong></h1>
                <p class="subtitle">Panel de gestión empresarial - Encuentra el talento que necesitas</p>
            </div>

            <!-- Tarjetas de estadísticas -->
            <div class="stats-grid">
                <div class="stat-card ofertas">
                    <div class="icon">💼</div>
                    <h3>Ofertas Activas</h3>
                    <div class="number"><?= $ofertasActivas ?></div>
                    <p class="description">Ofertas de trabajo publicadas</p>
                </div>

                <div class="stat-card solicitudes">
                    <div class="icon">📋</div>
                    <h3>Solicitudes Recibidas</h3>
                    <div class="number"><?= $totalSolicitudes ?></div>
                    <p class="description">Candidatos interesados en tus ofertas</p>
                </div>

                <div class="stat-card invitaciones">
                    <div class="icon">✉️</div>
                    <h3>Invitaciones Enviadas</h3>
                    <div class="number"><?= $invitacionesEnviadas ?></div>
                    <p class="description">Invitaciones directas a candidatos</p>
                </div>

                <div class="stat-card candidatos">
                    <div class="icon">👥</div>
                    <h3>Candidatos Disponibles</h3>
                    <div class="number"><?= $candidatosDisponibles ?></div>
                    <p class="description">Talento registrado en la plataforma</p>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="quick-actions">
                <h2>🚀 Acciones Rápidas</h2>
                <div class="actions-grid">
                    <a href="crear_oferta.php" class="action-button">
                        <div class="icon">➕</div>
                        <div class="label">Crear Nueva Oferta</div>
                    </a>
                    
                    <a href="ver_ofertas_empresa.php" class="action-button">
                        <div class="icon">📊</div>
                        <div class="label">Gestionar Ofertas</div>
                    </a>
                    
                    <a href="invitar_candidatos.php" class="action-button">
                        <div class="icon">🎯</div>
                        <div class="label">Invitar Candidatos</div>
                    </a>
                    
                    <a href="ver_solicitudes_empresa.php" class="action-button">
                        <div class="icon">📨</div>
                        <div class="label">Ver Solicitudes</div>
                    </a>
                </div>
            </div>

            <!-- Consejos para empresas -->
            <div class="tips-section">
                <h2>💡 Consejos para Atraer Talento</h2>
                <div class="tips-grid">
                    <div class="tip-card">
                        <div class="tip-icon">✍️</div>
                        <h3>Descripciones Claras</h3>
                        <p>Sé específico sobre las responsabilidades y requisitos del puesto para atraer candidatos ideales.</p>
                    </div>
                    <div class="tip-card">
                        <div class="tip-icon">🎯</div>
                        <h3>Invitaciones Directas</h3>
                        <p>Utiliza nuestro sistema de invitaciones para contactar directamente con los candidatos que te interesen.</p>
                    </div>
                    <div class="tip-card">
                        <div class="tip-icon">⚡</div>
                        <h3>Respuesta Rápida</h3>
                        <p>Los candidatos valoran las respuestas rápidas. Revisa regularmente las solicitudes recibidas.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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
        
        // Animación de entrada para las tarjetas
        const cards = document.querySelectorAll('.stat-card, .quick-actions, .tips-section');
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