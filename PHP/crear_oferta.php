<?php
require_once(__DIR__ . '/conexion.php');
session_start();

// Validar si es empresa
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header('Location: login.php');
    exit;
}

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $ubicacion = $_POST['ubicacion'] ?? '';
    $contrato = $_POST['tipo_contrato'] ?? '';
    $estado = 'activo'; // nuevo campo

    if ($titulo && $descripcion) {
        $stmt = $pdo->prepare("INSERT INTO puestodetrabajo (IdUsuario, Titulo, Descripcion, Ubicacion, TipoContrato, Estado) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['usuario_id'], $titulo, $descripcion, $ubicacion, $contrato, $estado]);
        $mensaje = "Oferta publicada exitosamente.";
    } else {
        $mensaje = "Todos los campos obligatorios deben estar completos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Oferta</title>
  <link rel="stylesheet" href="../CSS/crear_oferta.css">
</head>
<body>
  <div class="form-container">
    <h2>Crear Oferta de Trabajo</h2>
    <form method="POST">
      <label>Título del Puesto *</label>
      <input type="text" name="titulo" required>

      <label>Descripción *</label>
      <textarea name="descripcion" rows="6" required></textarea>

      <label>Ubicación</label>
      <input type="text" name="ubicacion">

      <label>Tipo de Contrato</label>
      <select name="tipo_contrato">
        <option>Tiempo completo</option>
        <option>Medio tiempo</option>
        <option>Temporal</option>
        <option>Prácticas</option>
      </select>

      <button type="submit">Publicar Oferta</button>
    </form>

    <?php if (!empty($mensaje)): ?>
      <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>
  </div>
</body>
</html>
