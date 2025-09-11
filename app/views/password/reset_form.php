<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - WorkFinderPro</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/styles_signup.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <img src="<?= URLROOT ?>/images/imagesolologo.png" alt="WorkFinderPro">
            <h2>Nueva Contraseña</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= URLROOT ?>/PasswordReset/actualizarContrasena">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <input type="password" name="password" placeholder="Nueva Contraseña" required>
                <input type="password" name="confirm_password" placeholder="Confirmar Contraseña" required>
                <button type="submit">Actualizar Contraseña</button>
            </form>
        </div>
    </div>
</body>
</html>