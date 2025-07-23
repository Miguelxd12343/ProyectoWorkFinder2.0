<?php
require 'auth_guard.php';
require_once(__DIR__ . '/conexion.php');

// Refuerzo de control de caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
session_start();

// Solo admin puede acceder
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 3) {
    header('Location: login.php');
    exit;
}

// Obtener ID del usuario a editar
if (!isset($_GET['id'])) {
    echo "ID de usuario no especificado.";
    exit;
}

$id = $_GET['id'];

// Obtener datos actuales del usuario
$stmt = $pdo->prepare("SELECT * FROM usuario WHERE IdUsuario = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $rol = $_POST['rol'] ?? '';

    if ($nombre && $email && $rol !== '') {
        $stmt = $pdo->prepare("UPDATE usuario SET Nombre = ?, Email = ?, IdRol = ? WHERE IdUsuario = ?");
        $stmt->execute([$nombre, $email, $rol, $id]);
        header("Location: dashboard_admin.php?msg=Usuario actualizado correctamente");
        exit;
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Usuario</title>
  <link rel="stylesheet" href="../CSS/formulario_admin.css">
  <link rel="icon" href="images/imagesolologo.png" type="image/png">
</head>
<body>
  <div class="form-container">
    <h2>Editar Usuario</h2>
    <?php if (!empty($error)): ?>
      <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
      <label>Nombre:</label>
      <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['Nombre']) ?>" required>

      <label>Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($usuario['Email']) ?>" required>

      <label>Rol:</label>
      <select name="rol" required>
        <option value="0" <?= $usuario['IdRol'] == 0 ? 'selected' : '' ?>>Usuario</option>
        <option value="1" <?= $usuario['IdRol'] == 1 ? 'selected' : '' ?>>Empresa</option>
        <option value="3" <?= $usuario['IdRol'] == 3 ? 'selected' : '' ?>>Administrador</option>
      </select>

      <button type="submit">Guardar Cambios</button>
    </form>
  </div>
</body>
</html>