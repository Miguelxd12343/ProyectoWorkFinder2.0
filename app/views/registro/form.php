<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - WorkFinderPro</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/styles_signup.css">
    <link rel="icon" href="images/imagesolologo.png" type="image/png">
</head>

<body>
    <div class="container">
        <div class="form-box">
            <a href="<?= URLROOT ?>">
                <img src="<?= URLROOT ?>/images/imagesolologo.png" alt="WorkFinderPro">
            </a>
            <h2>Crear Cuenta</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form id="signupForm" method="POST" action="<?= URLROOT ?>/Signup/registrarUsuario">
                <input type="text" name="nombre" id="nombre" placeholder="Nombre completo" required>
                <input type="email" name="email" id="email" placeholder="Correo electrónico" required>
                <input type="password" name="contrasena" id="password1" placeholder="Contraseña" required>
                <input type="password" name="confirm_password" id="password2" placeholder="Confirmar Contraseña" required>
                
                <!-- Campo oculto para rol candidato -->
                <input type="hidden" name="rol" value="2">

                <button type="submit">Crear Cuenta</button>
            </form>

            <!-- Enlaces de navegación -->
            <div class="form-links">
                <p>¿Eres una empresa? <a href="<?= URLROOT ?>/SignupEmpresa/mostrarFormulario" class="link-empresa">Regístrate aquí</a></p>
                <p>¿Ya tienes cuenta? <a href="<?= URLROOT ?>/Login/index">Iniciar Sesión</a></p>
            </div>
        </div>
    </div>

    <script src="<?= URLROOT ?>/public/js/validaciones_signup.js"></script>
</body>
</html>
