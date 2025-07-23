<?php
require 'auth_guard.php';
require_once(__DIR__ . '/conexion.php');

$userId = $_SESSION['usuario_id'];
$userName = $_SESSION['usuario_nombre'];

// Procesar aceptar o rechazar invitación
if ($_POST) {
    if (isset($_POST['accion']) && isset($_POST['id_invitacion'])) {
        $idInvitacion = $_POST['id_invitacion'];
        $accion = $_POST['accion'];
        
        try {
            if ($accion === 'aceptar') {
                // Obtener datos de la invitación
                $stmt = $pdo->prepare("
                    SELECT i.*, p.Titulo, u.Nombre as NombreEmpresa 
                    FROM invitaciones i
                    JOIN puestodetrabajo p ON i.IdPuesto = p.IdPuesto
                    JOIN usuario u ON i.IdEmpresa = u.IdUsuario
                    WHERE i.IdInvitacion = ? AND i.IdCandidato = ?
                ");
                $stmt->execute([$idInvitacion, $userId]);
                $invitacion = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($invitacion) {
                    // Crear solicitud de empleo
                    $stmt = $pdo->prepare("
                        INSERT INTO solicitudempleo (FechaEnvio, Estado, IdUsuario, IdPuestoTrabajo) 
                        VALUES (CURDATE(), 'Enviada', ?, ?)
                    ");
                    $stmt->execute([$userId, $invitacion['IdPuesto']]);
                    
                    // Eliminar la invitación
                    $stmt = $pdo->prepare("DELETE FROM invitaciones WHERE IdInvitacion = ?");
                    $stmt->execute([$idInvitacion]);
                    
                    $mensaje = "Invitación aceptada correctamente. Se ha creado tu solicitud de empleo.";
                }
            } elseif ($accion === 'rechazar') {
                // Solo eliminar la invitación
                $stmt = $pdo->prepare("DELETE FROM invitaciones WHERE IdInvitacion = ? AND IdCandidato = ?");
                $stmt->execute([$idInvitacion, $userId]);
                
                $mensaje = "Invitación rechazada.";
            }
            
            header("Location: invitaciones.php?msg=" . urlencode($mensaje));
            exit;
        } catch (PDOException $e) {
            $error = "Error al procesar la invitación: " . $e->getMessage();
        }
    }
}

// Obtener invitaciones del usuario
try {
    $stmt = $pdo->prepare("
        SELECT i.*, p.Titulo, p.Descripcion, p.Ubicacion, p.TipoContrato, 
               u.Nombre as NombreEmpresa, i.FechaInvitacion, i.Mensaje
        FROM invitaciones i
        JOIN puestodetrabajo p ON i.IdPuesto = p.IdPuesto
        JOIN usuario u ON i.IdEmpresa = u.IdUsuario
        WHERE i.IdCandidato = ?
        ORDER BY i.FechaInvitacion DESC
    ");
    $stmt->execute([$userId]);
    $invitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $invitaciones = [];
    $error = "Error al obtener las invitaciones.";
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
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitaciones - WorkFinderPro</title>
    <link rel="stylesheet" href="../CSS/dashboard_usuario.css">
    
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
            
            <?php if (isset($error)): ?>
                <div class="alert error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <h1>✉️ Mis Invitaciones</h1>
            <p class="subtitle">Invitaciones de empresas interesadas en tu perfil</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card invitaciones">
                <div class="icon">✉️</div>
                <h3>Total de Invitaciones</h3>
                <div class="number"><?= count($invitaciones) ?></div>
                <p class="description">Invitaciones que has recibido de empresas</p>
            </div>
            
            <div class="stat-card ofertas">
                <div class="icon">🎯</div>
                <h3>Oportunidades</h3>
                <div class="number"><?= count($invitaciones) ?></div>
                <p class="description">Empresas interesadas en tu talento</p>
            </div>
        </div>

        <!-- Lista de Invitaciones -->
        <div class="invitations-section">
            <?php if (empty($invitaciones)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <h2>No tienes invitaciones</h2>
                    <p>Las empresas interesadas en tu perfil aparecerán aquí</p>
                    <a href="perfil.php" class="action-button inline">
                        <div class="icon">👤</div>
                        <div class="label">Completar Perfil</div>
                    </a>
                </div>
            <?php else: ?>
                <h2 class="section-title">📨 Invitaciones Recibidas</h2>
                <?php foreach ($invitaciones as $invitacion): ?>
                    <div class="invitation-card">
                        <div class="invitation-header">
                            <div class="company-info">
                                <div class="company-icon">🏢</div>
                                <div class="company-details">
                                    <h3><?= htmlspecialchars($invitacion['NombreEmpresa']) ?></h3>
                                    <p class="invitation-time"><?= tiempoRelativo($invitacion['FechaInvitacion']) ?></p>
                                </div>
                            </div>
                            <div class="invitation-badge">
                                ✨ Invitación Directa
                            </div>
                        </div>

                        <div class="job-details">
                            <h4 class="job-title">💼 <?= htmlspecialchars($invitacion['Titulo']) ?></h4>
                            <div class="job-info">
                                <?php if ($invitacion['Ubicacion']): ?>
                                    <span class="info-item">📍 <?= htmlspecialchars($invitacion['Ubicacion']) ?></span>
                                <?php endif; ?>
                                <?php if ($invitacion['TipoContrato']): ?>
                                    <span class="info-item">📋 <?= htmlspecialchars($invitacion['TipoContrato']) ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($invitacion['Descripcion']): ?>
                                <div class="job-description">
                                    <?= nl2br(htmlspecialchars(substr($invitacion['Descripcion'], 0, 200))) ?>
                                    <?php if (strlen($invitacion['Descripcion']) > 200): ?>...<?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($invitacion['Mensaje']): ?>
                                <div class="invitation-message">
                                    <strong>💌 Mensaje de la empresa:</strong>
                                    <p><?= nl2br(htmlspecialchars($invitacion['Mensaje'])) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="invitation-actions">
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="id_invitacion" value="<?= $invitacion['IdInvitacion'] ?>">
                                <button type="submit" name="accion" value="aceptar" class="btn-accept" 
                                        onclick="return confirm('¿Estás seguro de que quieres aceptar esta invitación?')">
                                    ✅ Aceptar Invitación
                                </button>
                            </form>
                            
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="id_invitacion" value="<?= $invitacion['IdInvitacion'] ?>">
                                <button type="submit" name="accion" value="rechazar" class="btn-reject"
                                        onclick="return confirm('¿Estás seguro de que quieres rechazar esta invitación?')">
                                    ❌ Rechazar
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // JavaScript del sidebar (mismo que en dashboard)
    if (!document.querySelector('.sidebar-toggle')) {
        const toggleButton = document.createElement('button');
        toggleButton.className = 'sidebar-toggle';
        toggleButton.innerHTML = '☰';
        toggleButton.setAttribute('aria-label', 'Abrir menú');
        document.body.appendChild(toggleButton);
        
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
        
        const sidebar = document.querySelector('.sidebar');
        
        function openSidebar() {
            sidebar.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeSidebar() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        toggleButton.addEventListener('click', openSidebar);
        overlay.addEventListener('click', closeSidebar);
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                closeSidebar();
            }
        });
        
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 968) {
                    closeSidebar();
                }
            });
        });
    }
    
    // Animaciones
    const cards = document.querySelectorAll('.invitation-card, .stat-card, .empty-state');
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