<?php
// Verifica que sea un administrador (rol 3)
if ($_SESSION['usuario_rol'] != 3) {
    header('Location: login.php');
    exit;
}

// Variables que vienen desde el controlador:
// $empresas → listado de empresas
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gestionar Empresas - WorkFinderPro Admin</title>
  <link rel="stylesheet" href="../public/css/dashboard_admin.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="/ProyectoWorkfinder2.0/public/css/styles_Gestionar_Empresas.css">
  <link rel="icon" href="public/images/imagesolologo.png" type="image/png">
  <script>
    window.addEventListener("pageshow", function (event) {
      if (event.persisted || (performance.getEntriesByType("navigation")[0]?.type === "back_forward")) {
        window.location.reload();
      }
    });
  </script>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
  <h2><a href="<?= URLROOT ?>" class="logo-link">WorkFinderPro</a></h2>
  <ul>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard_Admin.php' ? 'active' : '' ?>">
      <a href="<?= URLROOT ?>/Admin/dashboard">🏠 Inicio</a>
    </li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'gestionar_empresas.php' ? 'active' : '' ?>">
      <a href="<?= URLROOT ?>/Admin/gestionarEmpresas">🏢 Gestionar Empresas</a>
    </li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'gestionar_candidatos.php' ? 'active' : '' ?>">
      <a href="<?= URLROOT ?>/Admin/gestionarCandidatos">👥 Gestionar Candidatos</a>
    </li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'estadisticas.php' ? 'active' : '' ?>">
      <a href="<?= URLROOT ?>/Admin/estadisticas">📊 Estadísticas</a>
    </li>
    <li><a href="<?= URLROOT ?>/Login/logout">🚪 Cerrar Sesión</a></li>
  </ul>
</div>

<div class="main">
  <div class="empresas-container">
    <div class="empresas-header">
      <h1>Gestionar Empresas</h1>
      <p>Administra y supervisa todas las empresas registradas en la plataforma</p>
    </div>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success">
        <?php
        switch($_GET['success']) {
          case 'empresa_editada':
            echo '✅ Empresa actualizada correctamente';
            break;
          case 'empresa_eliminada':
            echo '✅ Empresa eliminada correctamente';
            break;
          default:
            echo '✅ Operación realizada correctamente';
        }
        ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-error">
        <?php
        switch($_GET['error']) {
          case 'empresa_no_encontrada':
            echo '❌ Empresa no encontrada';
            break;
          case 'error_eliminacion':
            echo '❌ Error al eliminar la empresa';
            break;
          default:
            echo '❌ Ha ocurrido un error';
        }
        ?>
      </div>
    <?php endif; ?>

    <!-- Estadísticas rápidas -->
    <div class="estadisticas-empresas">
      <div class="stat-card">
        <div class="stat-icon">🏢</div>
        <div class="stat-content">
          <h3><?= count($empresas) ?></h3>
          <p>Empresas Registradas</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-content">
          <h3><?= count(array_filter($empresas, function($e) { return !empty($e['Email']); })) ?></h3>
          <p>Empresas Activas</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-content">
          <h3><?= date('M Y') ?></h3>
          <p>Período Actual</p>
        </div>
      </div>
    </div>

    <!-- Herramientas -->
    <div class="herramientas-container">
      <div class="busqueda-empresa">
        <input type="text" id="buscarEmpresa" placeholder="🔍 Buscar empresa por nombre o email..." />
      </div>
      <div class="acciones-rapidas">

        <button class="btn-actualizar" onclick="location.reload()">
          🔄 Actualizar
        </button>
      </div>
    </div>

    <!-- Listado de empresas -->
    <?php if (!empty($empresas)): ?>
      <div class="empresas-grid">
        <?php foreach ($empresas as $empresa): ?>
          <div class="empresa-card" data-empresa="<?= strtolower($empresa['Nombre'] . ' ' . $empresa['Email']) ?>">
            <div class="empresa-header">
              <div class="empresa-avatar">
                <span><?= strtoupper(substr($empresa['Nombre'], 0, 2)) ?></span>
              </div>
              <div class="empresa-info">
                <h3 class="empresa-nombre"><?= htmlspecialchars($empresa['Nombre']) ?></h3>
                <p class="empresa-email"><?= htmlspecialchars($empresa['Email']) ?></p>
                <span class="empresa-id">ID: <?= $empresa['IdUsuario'] ?></span>
              </div>
              <div class="empresa-status">
                <span class="status-badge active">Activa</span>
              </div>
            </div>
            
            <div class="empresa-body">
              <div class="empresa-detalles">
                <div class="detalle-item">
                  <span class="detalle-icon">📧</span>
                  <span class="detalle-label">Email de contacto</span>
                </div>
                <div class="detalle-item">
                  <span class="detalle-icon">🆔</span>
                  <span class="detalle-label">ID del sistema: <?= $empresa['IdUsuario'] ?></span>
                </div>
                <div class="detalle-item">
                  <span class="detalle-icon">🏢</span>
                  <span class="detalle-label">Empresa verificada</span>
                </div>
              </div>
            </div>

            <div class="empresa-footer">
              <div class="acciones-empresa">
                <a href="<?= URLROOT ?>/Admin/editarEmpresa/<?= $empresa['IdUsuario'] ?>" 
                   class="btn-accion btn-editar">
                  ✏️ Editar
                </a>
                <button onclick="confirmarEliminacion(<?= $empresa['IdUsuario'] ?>, '<?= htmlspecialchars($empresa['Nombre']) ?>')" 
                        class="btn-accion btn-eliminar">
                  🗑️ Eliminar
                </button>

              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="no-empresas">
        <div class="no-empresas-icon">🏢</div>
        <h3>No hay empresas registradas</h3>
        <p>Aún no se han registrado empresas en la plataforma. Las empresas aparecerán aquí cuando se registren.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div id="modalEliminacion" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>⚠️ Confirmar eliminación</h3>
      <span class="modal-close">&times;</span>
    </div>
    <div class="modal-body">
      <p>¿Estás seguro de que deseas eliminar la empresa <strong id="empresaAEliminar"></strong>?</p>
      <p class="advertencia">Esta acción no se puede deshacer y eliminará todos los datos relacionados.</p>
    </div>
    <div class="modal-footer">
      <button class="btn-cancelar">Cancelar</button>
      <a id="btnConfirmarEliminacion" href="#" class="btn-confirmar-eliminar">Sí, eliminar</a>
    </div>
  </div>
</div>

<script>
// Funciones de JavaScript
function confirmarEliminacion(id, nombre) {
  document.getElementById('empresaAEliminar').textContent = nombre;
  document.getElementById('btnConfirmarEliminacion').href = '<?= URLROOT ?>/Admin/eliminarEmpresa/' + id;
  document.getElementById('modalEliminacion').style.display = 'block';
}

function exportarEmpresas() {
  // Implementar exportación
  alert('Funcionalidad de exportación en desarrollo');
}

function verDetalles(id) {
  // Implementar vista de detalles
  alert('Ver detalles de empresa ID: ' + id);
}

// Búsqueda en tiempo real
document.getElementById('buscarEmpresa').addEventListener('input', function() {
  const query = this.value.toLowerCase();
  const empresas = document.querySelectorAll('.empresa-card');
  
  empresas.forEach(function(empresa) {
    const texto = empresa.dataset.empresa;
    if (texto.includes(query)) {
      empresa.style.display = 'block';
    } else {
      empresa.style.display = 'none';
    }
  });
});

// Modal
document.querySelector('.modal-close').addEventListener('click', function() {
  document.getElementById('modalEliminacion').style.display = 'none';
});

document.querySelector('.btn-cancelar').addEventListener('click', function() {
  document.getElementById('modalEliminacion').style.display = 'none';
});

// Cerrar modal al hacer clic fuera
window.addEventListener('click', function(event) {
  const modal = document.getElementById('modalEliminacion');
  if (event.target === modal) {
    modal.style.display = 'none';
  }
});
</script>

</body>
</html>