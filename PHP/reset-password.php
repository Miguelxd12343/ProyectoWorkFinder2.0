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
  <link rel="stylesheet" href="../CSS/reset-password.css">
</head>
<body>
  <form action="/ProyectoWorkFinder/PHP/update-password.php" method="POST">
  <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
  <label>Nueva contraseña:</label><br>
  <input type="password" name="password" required><br><br>
  <input type="submit" value="Actualizar contraseña">
</form>
</body>
</html>
