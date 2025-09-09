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
    <link rel="icon" href="<?= URLROOT ?>/images/imagesolologo.png" type="image/png">
    
<style>
        .solicitudes-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .solicitudes-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .solicitudes-header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }

        .solicitudes-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1em;
        }

        .solicitudes-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-left: 4px solid #667eea;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card h3 {
            color: #333;
            margin: 0;
            font-size: 2em;
            font-weight: bold;
        }

        .stat-card p {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 1.1em;
        }

        .solicitudes-lista {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .lista-header {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
        }

        .lista-header h2 {
            margin: 0;
            font-size: 1.8em;
            font-weight: 400;
        }

        .solicitud-item {
            padding: 25px 30px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s ease;
        }

        .solicitud-item:hover {
            background-color: #f8f9ff;
        }

        .solicitud-item:last-child {
            border-bottom: none;
        }

        .solicitud-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .solicitud-titulo {
            flex: 1;
            min-width: 300px;
        }

        .solicitud-titulo h3 {
            color: #333;
            margin: 0 0 5px 0;
            font-size: 1.4em;
            font-weight: 600;
        }

        .empresa-nombre {
            color: #666;
            font-size: 1.1em;
            font-weight: 500;
        }

        .solicitud-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-label {
            font-weight: 600;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        .estado-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .estado-pendiente {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .estado-aceptada {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #00b894;
        }

        .estado-rechazada {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #e74c3c;
        }

        .estado-cancelada {
            background-color: #f1f3f4;
            color: #5f6368;
            border: 1px solid #dadce0;
        }

        .solicitud-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-ver {
            background: #007bff;
            color: white;
        }

        .btn-ver:hover {
            background: #0056b3;
            color: white;
        }

        .btn-cancelar {
            background: #dc3545;
            color: white;
        }

        .btn-cancelar:hover {
            background: #c82333;
        }

        .btn-cancelar:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }

        .no-solicitudes {
            text-align: center;
            padding: 60px 30px;
        }

        .no-solicitudes-icon {
            font-size: 4em;
            color: #ddd;
            margin-bottom: 20px;
        }

        .no-solicitudes h3 {
            color: #666;
            margin: 0 0 10px 0;
            font-size: 1.5em;
        }

        .no-solicitudes p {
            color: #888;
            margin: 0 0 25px 0;
            font-size: 1.1em;
            line-height: 1.6;
        }

        .btn-explorar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: inline-block;
        }

        .btn-explorar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }

        .fecha-postulacion {
            color: #777;
            font-size: 0.95em;
        }

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

        .solicitud-item.processing {
            opacity: 0.6;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .solicitudes-container {
                padding: 15px;
            }
            
            .solicitudes-header {
                padding: 20px;
            }
            
            .solicitudes-header h1 {
                font-size: 2em;
            }
            
            .solicitud-item {
                padding: 20px 15px;
            }
            
            .solicitud-info {
                grid-template-columns: 1fr;
            }
            
            .solicitud-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

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