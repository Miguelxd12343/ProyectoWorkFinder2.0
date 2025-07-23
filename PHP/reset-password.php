<?php
require 'conexion.php';

if (!isset($_GET['token'])) {
    die("Token inválido.");
}

$token = $_GET['token'];
$stmt = $pdo->prepare("SELECT * FROM usuario WHERE reset_token = ? AND token_expiry > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("Token inválido o expirado.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Actualizar contraseña</title>
  <link rel="stylesheet" href="../CSS/reset_password.css">
  <link rel="icon" href="images/imagesolologo.png" type="image/png">
</head>
<body>
  <div class="mensaje-container">
  <img src="../images/imagesolologo.png" alt="Logo">
  <form action="update-password.php" method="POST">
  <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
  <p>Nueva contraseña:</p>
  <input id="contraseña" type="password" name="password" placeholder="Nueva Contraseña" required><br><br>
  <input id="boton" type="submit" value="Actualizar contraseña">
  </form>
</div>
</body>
</html>
