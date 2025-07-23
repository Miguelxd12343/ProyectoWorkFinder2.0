<?php
require_once(__DIR__ . '/conexion.php');
session_start();

// Validar si es empresa
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header('Location: login.php');
    exit;
}

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $ubicacion = $_POST['ubicacion'] ?? '';
    $contrato = $_POST['tipo_contrato'] ?? '';
    $estado = 'Activa'; // debe coincidir con la base de datos y dashboard

    if ($titulo && $descripcion) {
        $stmt = $pdo->prepare("INSERT INTO puestodetrabajo (IdUsuario, Titulo, Descripcion, Ubicacion, TipoContrato, Estado) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['usuario_id'], $titulo, $descripcion, $ubicacion, $contrato, $estado]);
        $mensaje = "Oferta publicada exitosamente.";
        $tipo_mensaje = "success";
    } else {
        $mensaje = "Todos los campos obligatorios deben estar completos.";
        $tipo_mensaje = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear Oferta - WorkFinderPro</title>
  <link rel="stylesheet" href="../CSS/crear_oferta.css">
  <link rel="icon" href="images/imagesolologo.png" type="image/png">
</head>
<body>
    <!-- Botón toggle para móviles -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
    
    <!-- Overlay para cerrar sidebar en móviles -->
    <div class="sidebar-overlay" onclick="closeSidebar()"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2><a href="../index.html" class="logo-link">WorkFinderPro</a></h2>
        <ul>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard_empresa.php' ? 'active' : '' ?>">
                <a href="dashboard_empresa.php">Inicio</a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'crear_oferta.php' ? 'active' : '' ?>">
                <a href="crear_oferta.php">Crear Oferta</a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'ver_ofertas_empresa.php' ? 'active' : '' ?>">
                <a href="ver_ofertas_empresa.php">Ver Ofertas Publicadas</a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'invitar_candidatos.php' ? 'active' : '' ?>">
                <a href="invitar_candidatos.php">Invitar Candidatos</a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'ver_solicitudes_empresa.php' ? 'active' : '' ?>">
                <a href="ver_solicitudes_empresa.php">Ver Solicitudes</a>
            </li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="main">
        <div class="form-container">
            <!-- Header -->
            <div class="header">
                <h1>📋 <strong>Crear Oferta de Trabajo</strong></h1>
                <p class="subtitle">Publica una nueva oportunidad laboral para atraer el mejor talento</p>
            </div>

            <!-- Formulario -->
            <div class="form-wrapper">
                <form method="POST">
                    <div class="form-group">
                        <label for="titulo">Título del Puesto</label>
                        <input type="text" id="titulo" name="titulo" placeholder="Ej: Desarrollador Full Stack Senior" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción del Puesto</label>
                        <textarea id="descripcion" name="descripcion" rows="6" placeholder="Describe las responsabilidades, requisitos y beneficios del puesto..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="ubicacion">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" placeholder="Ej: Madrid, España / Remoto">
                    </div>

                    <div class="form-group">
                        <label for="tipo_contrato">Tipo de Contrato</label>
                        <select id="tipo_contrato" name="tipo_contrato">
                            <option value="Tiempo completo">Tiempo completo</option>
                            <option value="Medio tiempo">Medio tiempo</option>
                            <option value="Temporal">Temporal</option>
                            <option value="Prácticas">Prácticas</option>
                        </select>
                    </div>

                    <button type="submit" class="submit-btn">
                        <span class="btn-text">Publicar Oferta</span>
                        <span class="btn-icon">🚀</span>
                    </button>
                </form>

                <!-- Mensaje de resultado -->
                <?php if (!empty($mensaje)): ?>
                    <div class="mensaje <?= $tipo_mensaje ?>">
                        <?= htmlspecialchars($mensaje) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }

        // Cerrar sidebar al cambiar tamaño de ventana
        window.addEventListener('resize', function() {
            if (window.innerWidth > 968) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.querySelector('.sidebar-overlay');
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        });
    </script>
</body>
</html>