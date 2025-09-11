<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Oferta - WorkFinderPro</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/css/crear_oferta.css">
    <link rel="icon" href="<?= URLROOT ?>/images/imagesolologo.png" type="image/png">
</head>
<body>
    <div class="sidebar" id="sidebar">
        <h2><a href="<?= URLROOT ?>" class="logo-link">WorkFinderPro</a></h2>
        <ul>
            <li>
                <a href="<?= URLROOT ?>/Empresa/dashboard">Inicio</a>
            </li>
            <li class="active">
                <a href="<?= URLROOT ?>/Empresa/crearOferta">Crear Oferta</a>
            </li>
            <li>
                <a href="<?= URLROOT ?>/Empresa/verOfertas">Ver Ofertas Publicadas</a>
            </li>
            <li>
                <a href="<?= URLROOT ?>/Empresa/invitarCandidatos">Invitar Candidatos</a>
            </li>
            <li><a href="<?= URLROOT ?>/Empresa/logout">Cerrar Sesión</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="form-container">
            <div class="header">
                <h1>📋 <strong>Crear Oferta de Trabajo</strong></h1>
                <p class="subtitle">Publica una nueva oportunidad laboral para atraer el mejor talento</p>
            </div>

            <div class="form-wrapper">
                <form method="POST">
                    <div class="form-group">
                        <label for="titulo">Título del Puesto</label>
                        <input type="text" id="titulo" name="titulo" placeholder="Ej: Desarrollador Full Stack Senior" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción del Puesto</label>
                        <textarea id="descripcion" name="descripcion" rows="6" placeholder="Describe las responsabilidades, requisitos y beneficios del puesto..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="ubicacion">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" placeholder="Ej: Madrid, España / Remoto">
                    </div>

                    <div class="form-group">
                        <label for="tipo_contrato">Tipo de Contrato</label>
                        <select id="tipo_contrato" name="tipo_contrato">
                            <option value="Tiempo completo">Tiempo completo</option>
                            <option value="Medio tiempo">Medio tiempo</option>
                            <option value="Temporal">Temporal</option>
                            <option value="Prácticas">Prácticas</option>
                        </select>
                    </div>

                    <button type="submit" class="submit-btn">
                        <span class="btn-text">Publicar Oferta</span>
                        <span class="btn-icon">🚀</span>
                    </button>
                </form>

                <?php if (!empty($mensaje)): ?>
                    <div class="mensaje <?= $tipo_mensaje ?>">
                        <?= htmlspecialchars($mensaje) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="<?= URLROOT ?>/js/empresa_sidebar.js"></script>
</body>
</html>