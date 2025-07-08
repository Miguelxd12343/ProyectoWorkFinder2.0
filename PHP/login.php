
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
require_once(__DIR__ . '/conexion.php');
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT IdUsuario, Contrasena, Nombre, IdRol FROM usuario WHERE Email = ?");
    $stmt->execute([$user]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($pass, $usuario['Contrasena'])) {
        $_SESSION['usuario_id'] = $usuario['IdUsuario'];
        $_SESSION['usuario_nombre'] = $usuario['Nombre'];
        $_SESSION['usuario_rol'] = $usuario['IdRol'];

        // Redirección según rol
        switch ($usuario['IdRol']) {
            case 1: // Empresa
                header('Location: ../PHP/dashboard_empresa.php');
                break;
            case 2: // Candidato
                header('Location: ../PHP/dashboard_usuario.php');
                break;
            case 3: // Administrador
                header('Location: ../PHP/dashboard_admin.php');
                break;
            default:
                $error = "Rol no reconocido.";
        }
        exit;
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}
?>

<!-- HTML igual al anterior -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WORKFINDERPRO Login</title>
  <link rel="stylesheet" href="../CSS/styles_login.css">
</head>
<body>
  <div class="container">
    <div class="login-box">
      <a href="../index.html" class="back-arrow">←</a>
      <img src="../images/imagesolologo.png" class="logo" alt="Logo">
      <h2>WORKFINDERPRO</h2>
      <h3>Login</h3>
      <form id="loginForm" method="POST" action="login.php">
        <label for="email">Email - correo</label>
        <input type="email" id="email" name="username" placeholder="Ingrese su correo" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>

        <button type="submit" class="login-button">Login</button>
        <p class="register-text">¿No tiene cuenta aún? <a href="../HTML/signup.html">CREAR CUENTA</a></p>
        <p class="register-text">¿Olvido su contraseña? <a href="../HTML/forgotpassword.html">RECUPERAR CONTRASEÑA</a></p>
      </form>
      <?php if (!empty($error)): ?>
        <p style="color: red; text-align: center;"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>
    </div>
    <div class="image-section">
      <img src="../images/4671 1.png" alt="Entrevista laboral">
    </div>
  </div>
</body>
</html>
