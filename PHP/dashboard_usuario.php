<?php
require 'auth_guard.php'; // Aquí ya se incluye session_start() si es necesario
require_once(__DIR__ . '/conexion.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Usuario</title>
  <link rel="stylesheet" href="../CSS/dashboard_usuario.css" />
</head>
<body>
  <div class="sidebar">
    <h2><a href="../index.html" class="logo-link">WorkFinderPro</a></h2>

    <ul>
      <li class="active"><a href="dashboard_usuario.php">Inicio</a></li>
      <li><a href="perfil.php">Perfil</a></li>
      <li><a href="ver_ofertas.php">Ofertas</a></li>
      <li><a href="#">Solicitudes</a></li>
      <li><a href="logout.php">Cerrar sesión</a></li>
    </ul>
  </div>

  <div class="main">
    <div class="header">
      
      <?php if (isset($_GET['msg'])): ?>
  <div class="alert">
    <?= htmlspecialchars($_GET['msg']) ?>
  </div>
<?php endif; ?>

      <h1>Bienvenido, <strong><?php echo $_SESSION['usuario_nombre']; ?></strong></h1>
    </div>

    <div class="cards">
      <!-- Ofertas activas -->
      <div class="card">
        <h3>Ofertas Activas</h3>
        <p>
          <?php
          try {
              $stmt = $pdo->prepare("SELECT COUNT(*) FROM puestodetrabajo WHERE Estado = 'Activa'");
              $stmt->execute();
              $ofertas = $stmt->fetchColumn();
              echo $ofertas . ' ofertas activas';
          } catch (PDOException $e) {
              echo "Error al consultar ofertas: " . $e->getMessage();
          }
          ?>
        </p>
      </div>

      <!-- Postulaciones realizadas -->
      <div class="card">
        <h3>Postulaciones Realizadas</h3>
        <p>
          <?php
          try {
              $stmt = $pdo->prepare("SELECT COUNT(*) FROM solicitudempleo WHERE IdUsuario = ?");
              $stmt->execute([$_SESSION['usuario_id']]);
              $postulaciones = $stmt->fetchColumn();
              echo $postulaciones . ' postulaciones realizadas';
          } catch (PDOException $e) {
              echo "Error al consultar postulaciones: " . $e->getMessage();
          }
          ?>
        </p>
      </div>
    </div>

    
    </div>
  </div>
</body>
</html>
