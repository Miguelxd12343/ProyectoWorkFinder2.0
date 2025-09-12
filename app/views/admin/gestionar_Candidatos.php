<?php
// Verifica que sea un administrador (rol 3)
if ($_SESSION['usuario_rol'] != 3) {
    header('Location: login.php');
    exit;
}

// Variables que vienen desde el controlador:
// $candidatos → listado de candidatos
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gestionar Candidatos - WorkFinderPro Admin</title>
  <link rel="stylesheet" href="../public/css/dashboard_admin.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="/ProyectoWorkfinder2.0/public/css/styles_Gestionar_Candidatos.css">
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
  <div class="candidatos-container">
    <div class="candidatos-header">
      <h1>Gestionar Candidatos</h1>
      <p>Administra y supervisa todos los candidatos registrados en la plataforma</p>
    </div>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success">
        <?php
        switch($_GET['success']) {
          case 'candidato_editado':
            echo '✅ Candidato actualizado correctamente';
            break;
          case 'candidato_eliminado':
            echo '✅ Candidato eliminado correctamente';
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
          case 'candidato_no_encontrado':
            echo '❌ Candidato no encontrado';
            break;
          case 'error_eliminacion':
            echo '❌ Error al eliminar el candidato';
            break;
          default:
            echo '❌ Ha ocurrido un error';
        }
        ?>
      </div>
    <?php endif; ?>

    <!-- Estadísticas rápidas -->
    <div class="estadisticas-candidatos">
      <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
          <h3><?= count($candidatos ?? []) ?></h3>
          <p>Candidatos Registrados</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-content">
          <h3><?= count(array_filter($candidatos ?? [], function($c) { return !empty($c['Email']); })) ?></h3>
          <p>Candidatos Activos</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">🎯</div>
        <div class="stat-content">
          <h3><?= count(array_filter($candidatos ?? [], function($c) { return !empty($c['Telefono']); })) ?></h3>
          <p>Con Perfil Completo</p>
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
      <div class="busqueda-candidato">
        <input type="text" id="buscarCandidato" placeholder="🔍 Buscar candidato por nombre o email..." />
      </div>
      <div class="filtros-candidatos">
      </div>
      <div class="acciones-rapidas">
        <button class="btn-actualizar" onclick="location.reload()">
          🔄 Actualizar
        </button>
      </div>
    </div>

    <!-- Listado de candidatos -->
    <?php if (!empty($candidatos)): ?>
      <div class="candidatos-grid">
        <?php foreach ($candidatos as $candidato): ?>
          <div class="candidato-card" data-candidato="<?= strtolower(($candidato['Nombre'] ?? '') . ' ' . ($candidato['Apellido'] ?? '') . ' ' . ($candidato['Email'] ?? '')) ?>">
            <div class="candidato-header">
              <div class="candidato-avatar">
                <span><?= strtoupper(substr(($candidato['Nombre'] ?? 'N'), 0, 1) . substr(($candidato['Apellido'] ?? 'A'), 0, 1)) ?></span>
              </div>
              <div class="candidato-info">
                <h3 class="candidato-nombre"><?= htmlspecialchars(($candidato['Nombre'] ?? '') . ' ' . ($candidato['Apellido'] ?? '')) ?></h3>
                <p class="candidato-email"><?= htmlspecialchars($candidato['Email'] ?? 'Sin email') ?></p>
                <span class="candidato-id">ID: <?= $candidato['IdUsuario'] ?? 'N/A' ?></span>
              </div>
              <div class="candidato-status">
                <span class="status-badge active">Activo</span>
              </div>
            </div>
            
            <div class="candidato-body">
              <div class="candidato-detalles">
                <div class="detalle-item">
                  <span class="detalle-icon">📱</span>
                  <span class="detalle-label">
                    <?= !empty($candidato['Telefono']) ? htmlspecialchars($candidato['Telefono']) : 'Sin teléfono' ?>
                  </span>
                </div>
                <div class="detalle-item">
                  <span class="detalle-icon">📍</span>
                  <span class="detalle-label">
                    <?= !empty($candidato['Direccion']) ? htmlspecialchars($candidato['Direccion']) : 'Sin dirección' ?>
                  </span>
                </div>
                <div class="detalle-item">
                  <span class="detalle-icon">🎂</span>
                  <span class="detalle-label">
                    <?= !empty($candidato['FechaNacimiento']) ? date('d/m/Y', strtotime($candidato['FechaNacimiento'])) : 'Fecha no disponible' ?>
                  </span>
                </div>
                <div class="detalle-item">
                  <span class="detalle-icon">🆔</span>
                  <span class="detalle-label">ID del sistema: <?= $candidato['IdUsuario'] ?? 'N/A' ?></span>
                </div>
              </div>
            </div>

            <div class="candidato-footer">
              <div class="acciones-candidato">
                <a href="<?= URLROOT ?>/Admin/editarCandidato/<?= $candidato['IdUsuario'] ?? '' ?>" 
                   class="btn-accion btn-editar">
                  ✏️ Editar
                </a>
                <button onclick="confirmarEliminacion(<?= $candidato['IdUsuario'] ?? 0 ?>, '<?= htmlspecialchars(($candidato['Nombre'] ?? '') . ' ' . ($candidato['Apellido'] ?? '')) ?>')" 
                        class="btn-accion btn-eliminar">
                  🗑️ Eliminar
                </button>

              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="no-candidatos">
        <div class="no-candidatos-icon">👥</div>
        <h3>No hay candidatos registrados</h3>
        <p>Aún no se han registrado candidatos en la plataforma. Los candidatos aparecerán aquí cuando se registren.</p>
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
      <p>¿Estás seguro de que deseas eliminar al candidato <strong id="candidatoAEliminar"></strong>?</p>
      <p class="advertencia">Esta acción no se puede deshacer y eliminará todos los datos relacionados incluido su CV y postulaciones.</p>
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
  document.getElementById('candidatoAEliminar').textContent = nombre;
  document.getElementById('btnConfirmarEliminacion').href = '<?= URLROOT ?>/Admin/eliminarCandidato/' + id;
  document.getElementById('modalEliminacion').style.display = 'block';
}

function exportarCandidatos() {
  // Implementar exportación
  alert('Funcionalidad de exportación en desarrollo');
}

function verDetalles(id) {
  // Implementar vista de detalles
  alert('Ver detalles de candidato ID: ' + id);
}

function verCV(id) {
  // Implementar vista de CV
  alert('Ver CV del candidato ID: ' + id);
}

// Búsqueda en tiempo real
document.getElementById('buscarCandidato').addEventListener('input', function() {
  const query = this.value.toLowerCase();
  const candidatos = document.querySelectorAll('.candidato-card');
  
  candidatos.forEach(function(candidato) {
    const texto = candidato.dataset.candidato;
    if (texto.includes(query)) {
      candidato.style.display = 'block';
    } else {
      candidato.style.display = 'none';
    }
  });
});

// Filtro por estado
document.getElementById('filtroEstado').addEventListener('change', function() {
  const estado = this.value;
  const candidatos = document.querySelectorAll('.candidato-card');
  
  candidatos.forEach(function(candidato) {
    if (estado === '') {
      candidato.style.display = 'block';
    } else {
      // Aquí puedes implementar la lógica de filtrado por estado
      // Por ahora mostraremos todos
      candidato.style.display = 'block';
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