<?php
require 'auth_guard.php';
require_once(__DIR__ . '/conexion.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mis Solicitudes - WorkFinderPro</title>
  <link rel="stylesheet" href="../CSS/dashboard_usuario.css" />
  
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
      justify-content: between;
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
    <h2><a href="../index.html" class="logo-link">WorkFinderPro</a></h2>
    <ul>
      <li><a href="dashboard_usuario.php">Inicio</a></li>
      <li><a href="perfil.php">Perfil</a></li>
      <li><a href="ver_ofertas.php">Ofertas</a></li>
      <li class="active"><a href="solicitudes.php">Solicitudes</a></li>
      <li><a href="logout.php">Cerrar sesión</a></li>
    </ul>
  </div>

  <div class="main">
    <div class="solicitudes-container">
      <div class="solicitudes-header">
        <h1>Mis Solicitudes</h1>
        <p>Gestiona y revisa todas tus postulaciones laborales</p>
      </div>

      <?php
      try {
          // Obtener estadísticas de solicitudes
          $stmt_stats = $pdo->prepare("
              SELECT 
                  COUNT(*) as total,
                  SUM(CASE WHEN Estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
                  SUM(CASE WHEN Estado = 'Aceptada' THEN 1 ELSE 0 END) as aceptadas,
                  SUM(CASE WHEN Estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas
              FROM solicitudempleo 
              WHERE IdUsuario = ?
          ");
          $stmt_stats->execute([$_SESSION['usuario_id']]);
          $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);

          if ($stats['total'] > 0): ?>
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
          <?php endif;

          // Obtener todas las solicitudes del usuario
          $stmt = $pdo->prepare("
              SELECT 
                  s.IdSolicitud,
                  s.FechaEnvio,
                  s.Estado,
                  p.Titulo,
                  p.Descripcion,
                  p.Ubicacion,
                  p.TipoContrato,
                  u.Nombre as NombreEmpresa
              FROM solicitudempleo s
              INNER JOIN puestodetrabajo p ON s.IdPuestoTrabajo = p.IdPuesto
              INNER JOIN usuario u ON p.IdUsuario = u.IdUsuario
              WHERE s.IdUsuario = ?
              ORDER BY s.FechaEnvio DESC
          ");
          $stmt->execute([$_SESSION['usuario_id']]);
          $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($solicitudes) > 0): ?>
            <div class="solicitudes-lista">
              <div class="lista-header">
                <h2>Historial de Postulaciones</h2>
              </div>

              <?php foreach ($solicitudes as $solicitud): ?>
                <div class="solicitud-item">
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
                      <span class="info-label">📍 Ubicación:</span>
                      <span class="info-value"><?= htmlspecialchars($solicitud['Ubicacion']) ?></span>
                    </div>
                    <div class="info-item">
                      <span class="info-label">💼 Contrato:</span>
                      <span class="info-value"><?= htmlspecialchars($solicitud['TipoContrato']) ?></span>
                    </div>
                    <div class="info-item">
                      <span class="info-label">📅 Postulado:</span>
                      <span class="info-value fecha-postulacion">
                        <?= date('d/m/Y', strtotime($solicitud['FechaEnvio'])) ?>
                      </span>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

          <?php else: ?>
            <div class="solicitudes-lista">
              <div class="no-solicitudes">
                <div class="no-solicitudes-icon">📋</div>
                <h3>¡Aún no tienes solicitudes!</h3>
                <p>
                  Parece que no has enviado ninguna postulación aún.<br>
                  ¿Qué esperas? ¡Hay muchas oportunidades esperándote!
                </p>
                <a href="ver_ofertas.php" class="btn-explorar">
                  🚀 Explorar Ofertas
                </a>
              </div>
            </div>
          <?php endif;

      } catch (PDOException $e) {
          echo '<div class="solicitudes-lista">';
          echo '<div class="no-solicitudes">';
          echo '<h3>Error al cargar solicitudes</h3>';
          echo '<p>Ocurrió un error al consultar tus postulaciones. Por favor, inténtalo más tarde.</p>';
          echo '</div>';
          echo '</div>';
      }
      ?>
    </div>
  </div>
</body>
</html>