<?php
// Verifica que sea un usuario (rol 2)
if ($_SESSION['usuario_rol'] != 2) {
    header('Location: login.php');
    exit;
}

// Variables que vienen desde el controlador:
// $stats → estadísticas de ofertas
// $ofertas → listado de ofertas filtradas
// $filtros → array con filtros aplicados
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
  <link rel="stylesheet" href="<?= URLROOT ?>/css/dashboard_usuario.css" />
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/styles_Ofertas_Usuario.css" />
  <link rel="icon" href="images/imagesolologo.png" type="image/png">
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
      <a href="<?= URLROOT ?>/Candidato/invitaciones">Invitaciones</a>
    </li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'solicitudes.php' ? 'active' : '' ?>">
      <a href="<?= URLROOT ?>/Candidato/postulaciones">Solicitudes</a>
    </li>
    <li><a href="<?= URLROOT ?>/Candidato/logout">Cerrar sesión</a></li>
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

    <!-- Estadísticas -->
  <div class="estadisticas-ofertas">
  <div class="stat-card">
    <h3><?= $stats['total_ofertas'] ?? 0 ?></h3>
    <p>Ofertas Activas</p>
  </div>
  <div class="stat-card">
    <h3><?= $stats['total_empresas'] ?? 0 ?></h3>
    <p>Empresas</p>
  </div>
  <div class="stat-card">
    <h3><?= $stats['mis_postulaciones'] ?? 0 ?></h3>
    <p>Mis Postulaciones</p>
  </div>
</div>

    <!-- Filtros -->
<div class="filtros-container filtros-card">
  <h3>🔍 Filtrar Ofertas</h3>
  <form method="GET" action="index.php">
    <input type="hidden" name="controller" value="Candidato">
    <input type="hidden" name="action" value="ofertas">

    <div class="filtros-grid">
      <div class="filtro-grupo">
        <label for="ubicacion">📍 Ubicación</label>
        <select name="ubicacion" id="ubicacion">
          <option value="">Todas las ubicaciones</option>
          <?php foreach ($filtros['ubicaciones'] as $ubicacion): ?>
            <option value="<?= htmlspecialchars($ubicacion) ?>" 
              <?= ($filtros['seleccionada_ubicacion'] == $ubicacion) ? 'selected' : '' ?>>
              <?= htmlspecialchars($ubicacion) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="filtro-grupo">
        <label for="tipo_contrato">💼 Tipo de Contrato</label>
        <select name="tipo_contrato" id="tipo_contrato">
          <option value="">Todos los tipos</option>
          <?php foreach ($filtros['contratos'] as $contrato): ?>
            <option value="<?= htmlspecialchars($contrato) ?>" 
              <?= ($filtros['seleccionado_contrato'] == $contrato) ? 'selected' : '' ?>>
              <?= htmlspecialchars($contrato) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="filtro-grupo">
        <label for="busqueda">🔎 Palabra clave</label>
        <input type="text" name="busqueda" id="busqueda" 
          placeholder="Ej: Desarrollador, Marketing..." 
          value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>">
      </div>


    </div>
  </form>
</div>


    <!-- Listado de ofertas -->
    <?php if (!empty($ofertas)): ?>
      <div class="ofertas-grid">
        <?php foreach ($ofertas as $oferta): ?>
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
                
                <?php if ($oferta['yaPostulado']): ?>
                  <button class="btn-postulado">✅ Ya Postulado</button>
                <?php else: ?>
                  <form method="POST" action="<?= URLROOT ?>/Candidato/postular" style="display: inline;">
                    <input type="hidden" name="id_puesto" value="<?= $oferta['IdPuesto'] ?>">
                    <button type="submit" class="btn-postular">🚀 Postularse</button>
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
          <?php if (!empty($filtros['seleccionada_ubicacion']) || 
                    !empty($filtros['seleccionado_contrato']) || 
                    !empty($filtros['busqueda'])): ?>
            No encontramos ofertas que coincidan con tus criterios de búsqueda. Intenta ajustar los filtros.
          <?php else: ?>
            Actualmente no hay ofertas activas. ¡Vuelve pronto para nuevas oportunidades!
          <?php endif; ?>
        </p>
      </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
