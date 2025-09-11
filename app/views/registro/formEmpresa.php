<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empresa - WorkFinderPro</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/styles_signup.css">
    <link rel="icon" href=../public/images/imagesolologo.png type="image/png">
</head>
<body>
    <div class="container">
        <div class="form-box empresa-form">
            <a href="<?= URLROOT ?>">
                <img src="<?= URLROOT ?>../public/images/imagesolologo.png" alt="WorkFinderPro">
            </a>
            <h2>Registro de Empresa</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form id="signupEmpresaForm" method="POST" action="<?= URLROOT ?>/SignupEmpresa/registrarEmpresa">
                <input type="text" name="nombre_empresa" id="nombre_empresa" placeholder="Nombre de la empresa" required>
                <input type="email" name="email" id="email" placeholder="Correo electrónico corporativo" required>
                <input type="password" name="contrasena" id="password1" placeholder="Contraseña" required>
                <input type="password" name="confirm_password" id="password2" placeholder="Confirmar Contraseña" required>
                
                <input type="text" name="direccion" id="direccion" placeholder="Dirección de la empresa" required>
                <input type="text" name="nit_cif" id="nit_cif" placeholder="NIT o CIF" required>
                <input type="tel" name="telefono" id="telefono" placeholder="Teléfono de contacto" required>
                
                <!-- Campo oculto para rol empresa -->
                <input type="hidden" name="rol" value="1">

                <button type="submit">Registrar Empresa</button>
            </form>

            <!-- Enlaces de navegación -->
            <div class="form-links">
                <p>¿Eres candidato? <a href="<?= URLROOT ?>/Signup/mostrarFormulario" class="link-candidato">Regístrate aquí</a></p>
                <p>¿Ya tienes cuenta? <a href="<?= URLROOT ?>/Login/index">Iniciar Sesión</a></p>
            </div>
        </div>
    </div>

    <script src="<?= URLROOT ?>/public/js/validaciones_signup_empresa.js"></script>
</body>
</html>