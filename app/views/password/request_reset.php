<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - WorkFinderPro</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/styles_signup.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <img src="<?= URLROOT ?>/images/imagesolologo.png" alt="WorkFinderPro">
            <h2>Recuperar Contraseña</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= URLROOT ?>/PasswordReset/enviarEnlaceReset">
                <input type="email" name="email" placeholder="Tu correo electrónico" required>
                <button type="submit">Enviar Enlace</button>
            </form>

            <div class="form-links">
                <p><a href="<?= URLROOT ?>/Login/index">Volver al Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>