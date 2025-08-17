<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - WorkFinderPro</title>
    <link rel="stylesheet" href="/public/css/styles_signup.css">
    <link rel="icon" href="images/imagesolologo.png" type="image/png">
</head>
<script src="/public/js/validaciones_signup.js"></script>
<body>
    <div class="container">
        <div class="form-box">
            <a href="../index.html">
                <img src="../images/imagesolologo.png" alt="WorkFinderPro">
            </a>
            <h2>Crear Cuenta</h2>
            <form id="signupForm" method="POST" action="../PHP/signup.php">
                <input type="text" name="nombre" id="nombre" placeholder="Nombre" required>
                <input type="email" name="email" id="email" placeholder="Correo electrónico" required>
                <input type="password" name="contrasena" id="password1" placeholder="Contraseña" required>
                <input type="password" name="confirm_password" id="password2" placeholder="Confirmar Contraseña" required>

                <select name="rol" id="rol" required>
                    <option value="">Seleccione un rol</option>
                    <option value="1">Empresa</option>
                    <option value="2">Candidato</option>
                </select>

                <!-- Campos adicionales solo para Empresa -->
                <div id="empresaExtra" style="display: none;">
                    <input type="text" name="direccion_empresa" id="direccion_empresa" placeholder="Dirección de la empresa">
                    <input type="text" name="identificacion_fiscal" id="identificacion_fiscal" placeholder="NIT o CIF">
                </div>

                <button type="submit">Crear</button>
            </form>
        </div>
    </div>
</body>
</html>
