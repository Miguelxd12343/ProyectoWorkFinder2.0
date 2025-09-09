<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WORKFINDERPRO Login</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/styles_login.css">
  <link rel="icon" href="images/imagesolologo.png" type="image/png">
</head>
<body>
  <div class="container">
    <div class="login-box">
      <a href="<?= URLROOT ?>" class="back-arrow">←</a>
      <img src="../images/imagesolologo.png" class="logo" alt="Logo">
      <h2>WORKFINDERPRO</h2>
      <h3>Login</h3>
      <form id="loginForm" method="POST" action="<?= URLROOT ?>/Login/autenticar">
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