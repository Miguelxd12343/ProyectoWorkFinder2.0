<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Solicitudes - WorkFinderPro</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/css/dashboard_usuario.css">
      <link rel="stylesheet" href="<?= URLROOT ?>/public/css/styles_Solicitudes_Usuario.css" />
    <link rel="icon" href="<?= URLROOT ?>/images/imagesolologo.png" type="image/png">

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
    <h2><a href="<?= URLROOT ?>" class="logo-link">WorkFinderPro</a></h2>
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
            <a href="<?= URLROOT ?>/Candidato/invitaciones">Invitaciones</a>
        </li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'postulaciones.php' ? 'active' : '' ?>">
            <a href="<?= URLROOT ?>/Candidato/postulaciones">Solicitudes</a>
        </li>
        <li><a href="<?= URLROOT ?>/Login/logout">Cerrar sesión</a></li>
    </ul>
</div>

<div class="main">
    <div class="solicitudes-container">
        <div class="solicitudes-header">
            <h1>Mis Solicitudes</h1>
            <p>Gestiona y revisa todas tus postulaciones laborales</p>
        </div>

        <?php if (isset($mensaje)): ?>
            <div class="alert" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert error" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($stats['total'] > 0): ?>
            <div class="solicitudes-stats">
                <div class="stat-card">
                    <h3><?= $stats['total'] ?></h3>
                    <p>Total Solicitudes</p>
                </div>
                <div class="stat-card">
                    <h3><?= $stats['pendientes'] ?></h3>
                    <p>Pendientes</p>
                </div>
                <div class="stat-card">
                    <h3><?= $stats['aceptadas'] ?></h3>
                    <p>Aceptadas</p>
                </div>
                <div class="stat-card">
                    <h3><?= $stats['rechazadas'] ?></h3>
                    <p>Rechazadas</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="solicitudes-lista">
            <?php if (count($solicitudes) > 0): ?>
                <div class="lista-header">
                    <h2>Historial de Postulaciones</h2>
                </div>

                <?php foreach ($solicitudes as $solicitud): ?>
                    <div class="solicitud-item" data-id="<?= $solicitud['IdSolicitud'] ?>">
                        <div class="solicitud-header">
                            <div class="solicitud-titulo">
                                <h3><?= htmlspecialchars($solicitud['Titulo']) ?></h3>
                                <div class="empresa-nombre"><?= htmlspecialchars($solicitud['NombreEmpresa']) ?></div>
                            </div>
                            <div class="estado-badge estado-<?= strtolower($solicitud['Estado']) ?>">
                                <?= htmlspecialchars($solicitud['Estado']) ?>
                            </div>
                        </div>

                        <div class="solicitud-info">
                            <div class="info-item">
                                <span class="info-label">Ubicación:</span>
                                <span class="info-value"><?= htmlspecialchars($solicitud['Ubicacion']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Contrato:</span>
                                <span class="info-value"><?= htmlspecialchars($solicitud['TipoContrato']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Postulado:</span>
                                <span class="info-value fecha-postulacion">
                                    <?= date('d/m/Y', strtotime($solicitud['FechaEnvio'])) ?>
                                </span>
                            </div>
                        </div>

                        <div class="solicitud-actions">
                            <a href="<?= URLROOT ?>/Candidato/verSolicitud?id=<?= $solicitud['IdSolicitud'] ?>" 
                               class="btn-action btn-ver">
                                Ver Detalles
                            </a>
                            
                            <?php if ($solicitud['Estado'] === 'Pendiente'): ?>
                                <button type="button" 
                                        class="btn-action btn-cancelar solicitud-action-btn"
                                        data-action="cancelar" 
                                        data-id="<?= $solicitud['IdSolicitud'] ?>"
                                        onclick="return confirm('¿Estás seguro de que quieres cancelar esta solicitud?')">
                                    Cancelar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <div class="no-solicitudes">
                    <div class="no-solicitudes-icon">📋</div>
                    <h3>¡Aún no tienes solicitudes!</h3>
                    <p>
                        Parece que no has enviado ninguna postulación aún.<br>
                        ¿Qué esperas? ¡Hay muchas oportunidades esperándote!
                    </p>
                    <a href="<?= URLROOT ?>/Candidato/ofertas" class="btn-explorar">
                        Explorar Ofertas
                    </a>
                </div>
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
// JavaScript para solicitudes
class SolicitudModule {
    constructor() {
        this.urlRoot = '<?= URLROOT ?>';
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.setupAnimations();
    }
    
    bindEvents() {
        // Manejar acciones de solicitudes con AJAX
        $(document).on('click', '.solicitud-action-btn', this.handleSolicitudAction.bind(this));
        
        // Actualizar solicitudes cada 30 segundos
        setInterval(this.refreshSolicitudes.bind(this), 30000);
    }
    
    handleSolicitudAction(e) {
        e.preventDefault();
        
        const button = $(e.target);
        const action = button.data('action');
        const solicitudId = button.data('id');
        const item = button.closest('.solicitud-item');
        
        // Mostrar loading
        this.showLoading();
        
        // Deshabilitar botones
        item.find('.solicitud-action-btn').prop('disabled', true);
        
        // Enviar petición AJAX
        $.ajax({
            url: this.urlRoot + '/Candidato/procesarSolicitud',
            method: 'POST',
            data: {
                solicitud_id: solicitudId,
                action: action
            },
            success: (response) => {
                this.hideLoading();
                
                if (response.success) {
                    this.showAlert(response.message, 'success');
                    
                    if (action === 'cancelar') {
                        // Cambiar estado visual
                        item.find('.estado-badge')
                            .removeClass('estado-pendiente')
                            .addClass('estado-cancelada')
                            .text('Cancelada');
                        
                        // Remover botón de cancelar
                        button.remove();
                        
                        // Actualizar estadísticas
                        this.updateStats();
                    }
                    
                } else {
                    this.showAlert(response.message, 'error');
                    // Rehabilitar botones
                    item.find('.solicitud-action-btn').prop('disabled', false);
                }
            },
            error: (xhr, status, error) => {
                this.hideLoading();
                this.showAlert('Error de conexión. Intenta de nuevo.', 'error');
                // Rehabilitar botones
                item.find('.solicitud-action-btn').prop('disabled', false);
            }
        });
    }}