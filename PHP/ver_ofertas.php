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
  <title>Ofertas Disponibles</title>
  <link rel="stylesheet" href="../CSS/dashboard_usuario.css" />
</head>
<body>
  <div class="sidebar">
    <h2>WorkFinderPro</h2>
    <ul>
      <li><a href="dashboard_usuario.php">Inicio</a></li>
      <li><a href="perfil.php">Perfil</a></li>
      <li class="active"><a href="ver_ofertas.php">Ofertas</a></li>
      <li><a href="#">Solicitudes</a></li>
      <li><a href="logout.php">Cerrar sesión</a></li>
    </ul>
  </div>

  <div class="main">
    <div class="header">
      <h1>Ofertas Disponibles</h1>
      <?php if (isset($_GET['msg'])): ?>
        <div class="alert"><?= htmlspecialchars($_GET['msg']) ?></div>
      <?php endif; ?>
    </div>

    <div class="ofertas-lista">
      <?php
      try {
          $usuarioId = $_SESSION['usuario_id'];

          // CORREGIDO: cambiar IdPuestoTrabajo por IdPuesto
          $stmt = $pdo->query("SELECT IdPuesto, Titulo, Descripcion, Ubicacion, TipoContrato FROM puestodetrabajo WHERE Estado = 'Activa' ORDER BY FechaPublicacion DESC");

          while ($oferta = $stmt->fetch()) {
              // Verificar si ya está postulado a esta oferta
              $verificar = $pdo->prepare("SELECT COUNT(*) FROM solicitudempleo WHERE IdUsuario = ? AND IdPuestoTrabajo = ?");
              $verificar->execute([$usuarioId, $oferta['IdPuesto']]);
              $yaPostulado = $verificar->fetchColumn() > 0;

              echo "<div class='oferta'>";
              echo "<h3>" . htmlspecialchars($oferta['Titulo']) . "</h3>";
              echo "<p><strong>Ubicación:</strong> " . htmlspecialchars($oferta['Ubicacion']) . "</p>";
              echo "<p><strong>Tipo de Contrato:</strong> " . htmlspecialchars($oferta['TipoContrato']) . "</p>";
              echo "<p>" . nl2br(htmlspecialchars($oferta['Descripcion'])) . "</p>";

              if ($yaPostulado) {
                  echo "<button disabled class='btn-disabled'>Ya postulado</button>";
              } else {
                  echo "<form method='POST' action='postular_oferta.php'>";
                  echo "<input type='hidden' name='id_puesto' value='" . $oferta['IdPuesto'] . "'>";
                  echo "<button type='submit'>Postularse</button>";
                  echo "</form>";
              }

              echo "</div>";
          }
      } catch (PDOException $e) {
          echo "<p>Error al cargar ofertas: " . $e->getMessage() . "</p>";
      }
      ?>
    </div>
  </div>
</body>
</html>