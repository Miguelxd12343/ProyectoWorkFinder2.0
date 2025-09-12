<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Oferta - WorkFinderPro</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/css/crear_oferta.css">
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
                <h1>✏️ <strong>Editar Oferta de Trabajo</strong></h1>
                <p class="subtitle">Actualiza la información de tu oferta laboral</p>
            </div>

            <div class="form-wrapper">
                <form method="POST">
                    <div class="form-group">
                        <label for="titulo">Título del Puesto</label>
                        <input type="text" id="titulo" name="titulo" 
                               value="<?= htmlspecialchars($formData['Titulo'] ?? '') ?>" 
                               placeholder="Ej: Desarrollador Full Stack Senior" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción del Puesto</label>
                        <textarea id="descripcion" name="descripcion" rows="6" required 
                                  placeholder="Describe las responsabilidades, requisitos y beneficios del puesto..."><?= htmlspecialchars($formData['Descripcion'] ?? '') ?></textarea>
                        <div class="form-help">Mínimo 50 caracteres para una descripción completa</div>
                    </div>

                    <div class="form-group">
                        <label for="ubicacion">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" 
                               value="<?= htmlspecialchars($formData['Ubicacion'] ?? '') ?>"
                               placeholder="Ej: Madrid, España / Remoto">
                    </div>

                    <div class="form-group">
                        <label for="tipo_contrato">Tipo de Contrato</label>
                        <select id="tipo_contrato" name="tipo_contrato">
                            <option value="Tiempo completo" <?= ($formData['TipoContrato'] ?? '') === 'Tiempo completo' ? 'selected' : '' ?>>Tiempo completo</option>
                            <option value="Medio tiempo" <?= ($formData['TipoContrato'] ?? '') === 'Medio tiempo' ? 'selected' : '' ?>>Medio tiempo</option>
                            <option value="Temporal" <?= ($formData['TipoContrato'] ?? '') === 'Temporal' ? 'selected' : '' ?>>Temporal</option>
                            <option value="Prácticas" <?= ($formData['TipoContrato'] ?? '') === 'Prácticas' ? 'selected' : '' ?>>Prácticas</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <a href="<?= URLROOT ?>/Empresa/verOfertas" class="btn-cancel">Cancelar</a>
                        <button type="submit" class="submit-btn">
                            <span class="btn-text">Actualizar Oferta</span>
                            <span class="btn-icon">💾</span>
                        </button>
                    </div>
                </form>

                <?php if (!empty($mensaje)): ?>
                    <div class="mensaje <?= $tipo_mensaje ?>">
                        <?= htmlspecialchars($mensaje) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>