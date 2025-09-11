<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard - WorkFinderPro</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/dashboard_usuario.css" />
  <link rel="icon" href="images/imagesolologo.png" type="image/png">

  <!-- Evitar que Chrome muestre la página desde bfcache al volver atrás -->
  <script>
    window.addEventListener("pageshow", function (event) {
      if (event.persisted || (performance.getEntriesByType("navigation")[0]?.type === "back_forward")) {
        window.location.reload();
      }
    });
  </script>
</head>
<script src="<?= URLROOT ?>/public/js/dashboard_usuario.js"></script>
<body>
<div class="sidebar">
  <h2><a href="<?= URLROOT ?>" class="logo-link">WorkFinderPro</a></h2>
  <ul>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard_usuario.php' ? 'active' : '' ?>">
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
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'solicitudes.php' ? 'active' : '' ?>">
      <a href="<?= URLROOT ?>/Candidato/postulaciones">Solicitudes</a>
    </li>
    <li><a href="<?= URLROOT ?>/Candidato/logout">Cerrar sesión</a></li>
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
        <a href="<?= URLROOT ?>/Candidato/ofertas" class="action-button">
          <div class="icon">🔍</div>
          <div class="label">Buscar Ofertas</div>
        </a>
        
        <a href="<?= URLROOT ?>/Candidato/perfil" class="action-button">
          <div class="icon">👤</div>
          <div class="label">Mi Perfil</div>
        </a>
        
        <a href="<?= URLROOT ?>/Candidato/invitaciones" class="action-button">
          <div class="icon">✉️</div>
          <div class="label">Mis Invitaciones</div>
        </a>
        
        <a href="<?= URLROOT ?>/Candidato/postulaciones" class="action-button">
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
</body>
</html>