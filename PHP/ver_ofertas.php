<?php
require 'auth_guard.php';
require_once(__DIR__ . '/conexion.php');

// Verifica que sea un usuario (rol 2)
if ($_SESSION['usuario_rol'] != 2) {
    header('Location: login.php');
    exit;
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
  <title>Ofertas Disponibles - WorkFinderPro</title>
  <link rel="stylesheet" href="../CSS/dashboard_usuario.css" />
  
  <style>
    .ofertas-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    .ofertas-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
      position: relative;
      overflow: hidden;
    }

    .ofertas-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -20%;
      width: 100px;
      height: 100px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      transform: rotate(45deg);
    }

    .ofertas-header h1 {
      margin: 0;
      font-size: 2.5em;
      font-weight: 300;
      position: relative;
      z-index: 1;
    }

    .ofertas-header p {
      margin: 10px 0 0 0;
      opacity: 0.9;
      font-size: 1.1em;
      position: relative;
      z-index: 1;
    }

    .filtros-container {
      background: white;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
      margin-bottom: 30px;
    }

    .filtros-container h3 {
      margin: 0 0 20px 0;
      color: #333;
      font-size: 1.3em;
    }

    .filtros-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
      align-items: end;
    }

    .filtro-grupo {
      display: flex;
      flex-direction: column;
    }

    .filtro-grupo label {
      color: #555;
      font-weight: 600;
      margin-bottom: 5px;
    }

    .filtro-grupo select,
    .filtro-grupo input {
      padding: 10px;
      border: 2px solid #e1e8ed;
      border-radius: 8px;
      font-size: 1em;
      transition: border-color 0.3s ease;
    }

    .filtro-grupo select:focus,
    .filtro-grupo input:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-filtrar {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      font-size: 1em;
    }

    .btn-filtrar:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .ofertas-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
      gap: 25px;
      margin-bottom: 30px;
    }

    .oferta-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      position: relative;
    }

    .oferta-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .oferta-header {
      background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px 25px;
      position: relative;
    }

    .oferta-header::after {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 0;
      height: 0;
      border-left: 30px solid transparent;
      border-top: 30px solid rgba(255, 255, 255, 0.1);
    }

    .oferta-titulo {
      margin: 0;
      font-size: 1.4em;
      font-weight: 600;
      line-height: 1.3;
    }

    .oferta-empresa {
      margin: 5px 0 0 0;
      opacity: 0.9;
      font-size: 1em;
    }

    .oferta-body {
      padding: 25px;
    }

    .oferta-info {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 15px;
      margin-bottom: 20px;
    }

    .info-item {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .info-icon {
      font-size: 1.2em;
    }

    .info-text {
      color: #555;
      font-weight: 500;
    }

    .oferta-descripcion {
      color: #666;
      line-height: 1.6;
      margin-bottom: 25px;
      display: -webkit-box;
      -webkit-line-clamp: 4;
      -webkit-box-orient: vertical;
      overflow: hidden;
      min-height: 100px;
    }

    .oferta-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 20px;
      border-top: 1px solid #eee;
    }

    .fecha-publicacion {
      color: #888;
      font-size: 0.9em;
    }

    .btn-postular {
      background: linear-gradient(135deg, #00b894 0%, #00a085 100%);
      color: white;
      padding: 12px 25px;
      border: none;
      border-radius: 25px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 1em;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn-postular:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 184, 148, 0.4);
      color: white;
      text-decoration: none;
    }

    .btn-postulado {
      background: linear-gradient(135deg, #ddd 0%, #bbb 100%);
      color: #666;
      padding: 12px 25px;
      border: none;
      border-radius: 25px;
      font-weight: 600;
      font-size: 1em;
      cursor: not-allowed;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .estadisticas-ofertas {
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
      transition: transform 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
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

    .no-ofertas {
      text-align: center;
      padding: 60px 30px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .no-ofertas-icon {
      font-size: 4em;
      color: #ddd;
      margin-bottom: 20px;
    }

    .no-ofertas h3 {
      color: #666;
      margin: 0 0 10px 0;
      font-size: 1.5em;
    }

    .no-ofertas p {
      color: #888;
      margin: 0;
      font-size: 1.1em;
    }

    .alert {
      background: linear-gradient(135deg, #00b894 0%, #00a085 100%);
      color: white;
      padding: 15px 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      box-shadow: 0 5px 15px rgba(0, 184, 148, 0.3);
    }

    @media (max-width: 768px) {
      .ofertas-container {
        padding: 15px;
      }
      
      .ofertas-header {
        padding: 20px;
      }
      
      .ofertas-header h1 {
        font-size: 2em;
      }
      
      .ofertas-grid {
        grid-template-columns: 1fr;
      }
      
      .filtros-grid {
        grid-template-columns: 1fr;
      }
      
      .oferta-info {
        grid-template-columns: 1fr;
      }
      
      .oferta-footer {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
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
    <div class="ofertas-container">
      <div class="ofertas-header">
        <h1>Ofertas Disponibles</h1>
        <p>Encuentra tu próxima oportunidad profesional</p>
      </div>

      <?php if (isset($_GET['msg'])): ?>
        <div class="alert">
          <?= htmlspecialchars($_GET['msg']) ?>
        </div>
      <?php endif; ?>

      <?php
      try {
          $usuarioId = $_SESSION['usuario_id'];

          // Obtener estadísticas de ofertas
          $stmt_stats = $pdo->prepare("
              SELECT 
                  COUNT(*) as total_ofertas,
                  COUNT(DISTINCT p.IdUsuario) as total_empresas,
                  (SELECT COUNT(*) FROM solicitudempleo WHERE IdUsuario = ?) as mis_postulaciones
              FROM puestodetrabajo p 
              WHERE p.Estado = 'Activa'
          ");
          $stmt_stats->execute([$usuarioId]);
          $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);
      ?>

      <div class="estadisticas-ofertas">
        <div class="stat-card">
          <h3><?= $stats['total_ofertas'] ?></h3>
          <p>Ofertas Activas</p>
        </div>
        <div class="stat-card">
          <h3><?= $stats['total_empresas'] ?></h3>
          <p>Empresas</p>
        </div>
        <div class="stat-card">
          <h3><?= $stats['mis_postulaciones'] ?></h3>
          <p>Mis Postulaciones</p>
        </div>
      </div>

      <!-- Filtros -->
      <div class="filtros-container">
        <h3>🔍 Filtrar Ofertas</h3>
        <form method="GET" action="ver_ofertas.php">
          <div class="filtros-grid">
            <div class="filtro-grupo">
              <label for="ubicacion">Ubicación</label>
              <select name="ubicacion" id="ubicacion">
                <option value="">Todas las ubicaciones</option>
                <?php
                $stmt_ubicaciones = $pdo->query("SELECT DISTINCT Ubicacion FROM puestodetrabajo WHERE Estado = 'Activa' AND Ubicacion IS NOT NULL ORDER BY Ubicacion");
                while ($ubicacion = $stmt_ubicaciones->fetch()) {
                    $selected = (isset($_GET['ubicacion']) && $_GET['ubicacion'] == $ubicacion['Ubicacion']) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($ubicacion['Ubicacion']) . "' $selected>" . htmlspecialchars($ubicacion['Ubicacion']) . "</option>";
                }
                ?>
              </select>
            </div>
            
            <div class="filtro-grupo">
              <label for="tipo_contrato">Tipo de Contrato</label>
              <select name="tipo_contrato" id="tipo_contrato">
                <option value="">Todos los tipos</option>
                <?php
                $stmt_contratos = $pdo->query("SELECT DISTINCT TipoContrato FROM puestodetrabajo WHERE Estado = 'Activa' AND TipoContrato IS NOT NULL ORDER BY TipoContrato");
                while ($contrato = $stmt_contratos->fetch()) {
                    $selected = (isset($_GET['tipo_contrato']) && $_GET['tipo_contrato'] == $contrato['TipoContrato']) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($contrato['TipoContrato']) . "' $selected>" . htmlspecialchars($contrato['TipoContrato']) . "</option>";
                }
                ?>
              </select>
            </div>
            
            <div class="filtro-grupo">
              <label for="busqueda">Buscar por palabra clave</label>
              <input type="text" name="busqueda" id="busqueda" placeholder="Título, descripción..." value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
            </div>
            
            <div class="filtro-grupo">
              <button type="submit" class="btn-filtrar">🔍 Buscar</button>
            </div>
          </div>
        </form>
      </div>

      <?php
          // Construir la consulta con filtros
          $where_conditions = ["p.Estado = 'Activa'"];
          $params = [];

          if (!empty($_GET['ubicacion'])) {
              $where_conditions[] = "p.Ubicacion = ?";
              $params[] = $_GET['ubicacion'];
          }

          if (!empty($_GET['tipo_contrato'])) {
              $where_conditions[] = "p.TipoContrato = ?";
              $params[] = $_GET['tipo_contrato'];
          }

          if (!empty($_GET['busqueda'])) {
              $where_conditions[] = "(p.Titulo LIKE ? OR p.Descripcion LIKE ?)";
              $searchTerm = '%' . $_GET['busqueda'] . '%';
              $params[] = $searchTerm;
              $params[] = $searchTerm;
          }

          $where_clause = implode(' AND ', $where_conditions);

          $stmt = $pdo->prepare("
              SELECT 
                  p.IdPuesto, 
                  p.Titulo, 
                  p.Descripcion, 
                  p.Ubicacion, 
                  p.TipoContrato,
                  p.FechaPublicacion,
                  u.Nombre as NombreEmpresa
              FROM puestodetrabajo p
              INNER JOIN usuario u ON p.IdUsuario = u.IdUsuario
              WHERE $where_clause 
              ORDER BY p.FechaPublicacion DESC
          ");
          $stmt->execute($params);
          $ofertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if (count($ofertas) > 0): ?>
            <div class="ofertas-grid">
              <?php foreach ($ofertas as $oferta):
                  // Verificar si ya está postulado a esta oferta
                  $verificar = $pdo->prepare("SELECT COUNT(*) FROM solicitudempleo WHERE IdUsuario = ? AND IdPuestoTrabajo = ?");
                  $verificar->execute([$usuarioId, $oferta['IdPuesto']]);
                  $yaPostulado = $verificar->fetchColumn() > 0;
              ?>
                <div class="oferta-card">
                  <div class="oferta-header">
                    <h3 class="oferta-titulo"><?= htmlspecialchars($oferta['Titulo']) ?></h3>
                    <p class="oferta-empresa"><?= htmlspecialchars($oferta['NombreEmpresa']) ?></p>
                  </div>
                  
                  <div class="oferta-body">
                    <div class="oferta-info">
                      <div class="info-item">
                        <span class="info-icon">📍</span>
                        <span class="info-text"><?= htmlspecialchars($oferta['Ubicacion']) ?></span>
                      </div>
                      <div class="info-item">
                        <span class="info-icon">💼</span>
                        <span class="info-text"><?= htmlspecialchars($oferta['TipoContrato']) ?></span>
                      </div>
                    </div>
                    
                    <div class="oferta-descripcion">
                      <?= nl2br(htmlspecialchars($oferta['Descripcion'])) ?>
                    </div>
                    
                    <div class="oferta-footer">
                      <span class="fecha-publicacion">
                        📅 <?= date('d/m/Y', strtotime($oferta['FechaPublicacion'])) ?>
                      </span>
                      
                      <?php if ($yaPostulado): ?>
                        <button class="btn-postulado">
                          ✅ Ya Postulado
                        </button>
                      <?php else: ?>
                        <form method="POST" action="postular_oferta.php" style="display: inline;">
                          <input type="hidden" name="id_puesto" value="<?= $oferta['IdPuesto'] ?>">
                          <button type="submit" class="btn-postular">
                            🚀 Postularse
                          </button>
                        </form>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

          <?php else: ?>
            <div class="no-ofertas">
              <div class="no-ofertas-icon">💼</div>
              <h3>No hay ofertas disponibles</h3>
              <p>
                <?php if (!empty($_GET['ubicacion']) || !empty($_GET['tipo_contrato']) || !empty($_GET['busqueda'])): ?>
                  No encontramos ofertas que coincidan con tus criterios de búsqueda. Intenta ajustar los filtros.
                <?php else: ?>
                  Actualmente no hay ofertas activas. ¡Vuelve pronto para nuevas oportunidades!
                <?php endif; ?>
              </p>
            </div>
          <?php endif;

      } catch (PDOException $e) {
          echo '<div class="no-ofertas">';
          echo '<div class="no-ofertas-icon">⚠️</div>';
          echo '<h3>Error al cargar ofertas</h3>';
          echo '<p>Ocurrió un error al consultar las ofertas disponibles. Por favor, inténtalo más tarde.</p>';
          echo '</div>';
      }
      ?>
    </div>
  </div>
</body>
</html>