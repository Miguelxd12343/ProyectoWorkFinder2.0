<?php

if (!isset($nombreEmpresa)) {
    header("Location: " . URLROOT . "/DashboardEmpresa/index");
    exit;
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
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/dashboard_empresa.css">
    <link rel="icon" href="images/imagesolologo.png" type="image/png">
    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        });
    </script>
</head>
<script src="<?= URLROOT ?>/public/js/dashboard_empresa.js"></script>
<body>
    <div class="sidebar">
        <h2><a href="<?= URLROOT ?>" class="logo-link">WorkFinderPro</a></h2>
        <ul>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard_empresa.php' ? 'active' : '' ?>">
                <a href="index">Inicio</a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'crear_oferta.php' ? 'active' : '' ?>">
                <a href="<?= URLROOT ?>/Empresa/crearOferta">Crear Oferta</a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'ver_ofertas_empresa.php' ? 'active' : '' ?>">
                <a href="<?= URLROOT ?>/Empresa/verOfertas">Ver Ofertas Publicadas</a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'invitar_candidatos.php' ? 'active' : '' ?>">
                <a href="<?= URLROOT ?>/Empresa/invitarCandidatos">Invitar Candidatos</a>
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
                    <a href="<?= URLROOT ?>/Empresa/crearOferta" class="action-button">
                        <div class="icon">➕</div>
                        <div class="label">Crear Nueva Oferta</div>
                    </a>
                    
                    <a href="<?= URLROOT ?>/Empresa/verOfertas" class="action-button">
                        <div class="icon">📊</div>
                        <div class="label">Gestionar Ofertas</div>
                    </a>
                    
                    <a href="<?= URLROOT ?>/Empresa/invitarCandidatos" class="action-button">
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
</body>
</html>
