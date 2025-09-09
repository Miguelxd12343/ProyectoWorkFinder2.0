<?php
// Función helper para tiempo relativo
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
    <link rel="stylesheet" href="<?= URLROOT ?>/css/dashboard_usuario.css">
    <link rel="icon" href="<?= URLROOT ?>/images/imagesolologo.png" type="image/png">
    
    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted || (performance.getEntriesByType("navigation")[0]?.type === "back_forward")) {
                window.location.reload();
            }
        });
    </script>
    
    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-spinner {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .invitation-card.processing {
            opacity: 0.6;
            pointer-events: none;
        }

        .invitation-action-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h2><a href="<?= URLROOT ?>/index.html" class="logo-link">WorkFinderPro</a></h2>
    <ul>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            <a href="<?= URLROOT ?>/Candidato/dashboard">Inicio</a>
        </li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'perfil.php' ? 'active' : '' ?>">
            <a href="<?= URLROOT ?>/Candidato/perfil">Perfil</a>
        </li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'ver_ofertas.php' ? 'active' : '' ?>">
            <a href="<?= URLROOT ?>/Candidato/ofertas">Ofertas</a>
        </li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'invitaciones.php' ? 'active' : '' ?>">
            <a href="<?= URLROOT ?>/Candidato/invitaciones">Invitaciones✉️</a>
        </li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'postulaciones.php' ? 'active' : '' ?>">
            <a href="<?= URLROOT ?>/Candidato/postulaciones">Solicitudes</a>
        </li>
        <li><a href="<?= URLROOT ?>/Login/logout">Cerrar sesión</a></li>
    </ul>
</div>

<div class="main">
    <div class="dashboard-container">
        <div class="header">
            <?php if (isset($mensaje)): ?>
                <div class="alert">
                    <?= htmlspecialchars($mensaje) ?>
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
                <div class="number"><?= $stats['total_invitations'] ?></div>
                <p class="description">Invitaciones que has recibido de empresas</p>
            </div>
            
            <div class="stat-card ofertas">
                <div class="icon">🎯</div>
                <h3>Oportunidades</h3>
                <div class="number"><?= $stats['opportunities'] ?></div>
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
                    <a href="<?= URLROOT ?>/Candidato/perfil" class="action-button inline">
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
                            <form method="post" action="<?= URLROOT ?>/Candidato/invitaciones" style="display: inline;">
                                <input type="hidden" name="id_invitacion" value="<?= $invitacion['IdInvitacion'] ?>">
                                <button type="submit" name="accion" value="aceptar" class="btn-accept invitation-action-btn" 
                                        data-action="aceptar" data-id="<?= $invitacion['IdInvitacion'] ?>"
                                        onclick="return confirm('¿Estás seguro de que quieres aceptar esta invitación?')">
                                    ✅ Aceptar Invitación
                                </button>
                            </form>
                            
                            <form method="post" action="<?= URLROOT ?>/Candidato/invitaciones" style="display: inline;">
                                <input type="hidden" name="id_invitacion" value="<?= $invitacion['IdInvitacion'] ?>">
                                <button type="submit" name="accion" value="rechazar" class="btn-reject invitation-action-btn"
                                        data-action="rechazar" data-id="<?= $invitacion['IdInvitacion'] ?>"
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

<!-- Loading overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="loading-spinner">
        <div class="spinner"></div>
        <p>Procesando...</p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// JavaScript integrado en la vista
class InvitationModule {
    constructor() {
        this.urlRoot = '<?= URLROOT ?>';
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.setupAnimations();
    }
    
    bindEvents() {
        // Manejar acciones de invitaciones con AJAX
        $(document).on('click', '.invitation-action-btn', this.handleInvitationAction.bind(this));
        
        // Sidebar móvil (igual que tu código original)
        this.setupSidebar();
        
        // Actualizar invitaciones automáticamente cada 30 segundos
        setInterval(this.refreshInvitations.bind(this), 30000);
    }
    
    handleInvitationAction(e) {
        e.preventDefault();
        
        const button = $(e.target);
        const action = button.data('action');
        const invitationId = button.data('id');
        const card = button.closest('.invitation-card');
        
        // Confirmar acción ya está en el onclick del HTML
        
        // Mostrar loading
        this.showLoading();
        
        // Deshabilitar botones
        card.find('.invitation-action-btn').prop('disabled', true);
        
        // Enviar petición AJAX
        $.ajax({
            url: this.urlRoot + '/Candidato/procesarInvitacion',
            method: 'POST',
            data: {
                id_invitacion: invitationId,
                accion: action
            },
            success: (response) => {
                this.hideLoading();
                
                if (response.success) {
                    this.showAlert(response.message, 'success');
                    
                    // Animar y remover la tarjeta
                    card.addClass('processing');
                    setTimeout(() => {
                        card.fadeOut(500, () => {
                            card.remove();
                            this.updateStats();
                            this.checkEmptyState();
                        });
                    }, 1000);
                    
                } else {
                    this.showAlert(response.message, 'error');
                    // Rehabilitar botones
                    card.find('.invitation-action-btn').prop('disabled', false);
                }
            },
            error: (xhr, status, error) => {
                this.hideLoading();
                this.showAlert('Error de conexión. Intenta de nuevo.', 'error');
                // Rehabilitar botones
                card.find('.invitation-action-btn').prop('disabled', false);
            }
        });
    }
    
    refreshInvitations() {
        $.ajax({
            url: this.urlRoot + '/Candidato/invitacionesApi',
            method: 'GET',
            success: (data) => {
                this.updateStatsDisplay(data.stats);
            },
            error: (xhr, status, error) => {
                console.error('Error refreshing invitations:', error);
            }
        });
    }
    
    updateStatsDisplay(stats) {
        $('.stat-card.invitaciones .number').text(stats.total_invitations);
        $('.stat-card.ofertas .number').text(stats.opportunities);
    }
    
    updateStats() {
        // Actualizar contadores localmente
        const currentCount = $('.invitation-card').length - 1; // -1 porque ya removimos una
        $('.stat-card.invitaciones .number').text(currentCount);
        $('.stat-card.ofertas .number').text(currentCount);
    }
    
    checkEmptyState() {
        if ($('.invitation-card').length === 0) {
            $('.invitations-section').html(`
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <h2>No tienes invitaciones</h2>
                    <p>Las empresas interesadas en tu perfil aparecerán aquí</p>
                    <a href="${this.urlRoot}/Candidato/perfil" class="action-button inline">
                        <div class="icon">👤</div>
                        <div class="label">Completar Perfil</div>
                    </a>
                </div>
            `);
        }
    }
    
    setupSidebar() {
        // Tu código JavaScript original del sidebar
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
    }
    
    setupAnimations() {
        // Animaciones de entrada
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
    }
    
    showLoading() {
        $('#loadingOverlay').fadeIn(300);
    }
    
    hideLoading() {
        $('#loadingOverlay').fadeOut(300);
    }
    
    showAlert(message, type = 'info') {
        // Remover alertas anteriores
        $('.alert').remove();
        
        // Crear nueva alerta
        const alertClass = type === 'success' ? 'alert' : 'alert error';
        const alertHtml = `<div class="${alertClass}" style="display: none;">${message}</div>`;
        
        $('.header').prepend(alertHtml);
        $('.alert').slideDown(300);
        
        // Auto-hide después de 5 segundos
        setTimeout(() => {
            $('.alert').slideUp(300);
        }, 5000);
    }
}

// Inicializar módulo
$(document).ready(() => new InvitationModule());
</script>
</body>
</html>