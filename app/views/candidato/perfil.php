<?php
$nombreSesion   = $data['nombreSesion'] ?? '';
$mensaje        = $data['mensaje'] ?? '';
$tipo_mensaje   = $data['tipo_mensaje'] ?? '';
$perfil         = $data['perfil'] ?? null;
$esNuevoPerfil  = $data['esNuevoPerfil'] ?? true;
$bloquearNombre = $data['bloquearNombre'] ?? false;
$bloquearEdad   = $data['bloquearEdad'] ?? false;
$bloquearCedula = $data['bloquearCedula'] ?? false;
$errorCedula    = $data['errorCedula'] ?? '';
$edadCalculada  = $data['edadCalculada'] ?? '';
$totalSolicitudes = $data['totalSolicitudes'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mi Perfil - WorkFinderPro</title>
  <link rel="stylesheet" href="<?= URLROOT ?>/css/dashboard_usuario.css" />
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/styles_Perfil_Usuario.css" />
  <link rel="icon" href="images/imagesolologo.png" type="image/png">

  
</head>
  <script src="<?= URLROOT ?>/public/js/perfil_usuario.js"></script>
<body>
<div class="sidebar">
  <h2><a href="<?= URLROOT ?>" class="logo-link">WorkFinderPro</a></h2>
  <ul>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard_usuario.php' ? 'active' : '' ?>">
      <a href="<?= URLROOT ?>/Candidato/dashboard">Inicio</a>
    </li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'perfil.php' ? 'active' : '' ?>">
      <a href="<?= URLROOT ?>/Candidato/perfil">Perfil</a>
    </li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'ver_ofertas.php' ? 'active' : '' ?>">
      <a href="<?= URLROOT ?>/Candidato/ofertas">Ofertas</a>
    </li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'invitaciones.php' ? 'active' : '' ?>">
      <a href="<?= URLROOT ?>/Candidato/invitaciones">Invitaciones✉️</a>
    </li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'solicitudes.php' ? 'active' : '' ?>">
      <a href="<?= URLROOT ?>/Candidato/postulaciones">Solicitudes</a>
    </li>
    <li><a href="<?= URLROOT ?>/Candidato/logout">Cerrar sesión</a></li>
  </ul>
</div>

  <div class="main">
    <div class="perfil-container">
      <div class="perfil-header">
        <h1>Mi Perfil Profesional</h1>
        <p><?= $esNuevoPerfil ? 'Completa tu información para mejorar tus oportunidades' : 'Mantén tu información actualizada' ?></p>
      </div>

      <?php if ($mensaje): ?>
        <div class="mensaje <?= isset($tipo_mensaje) ? $tipo_mensaje : 'success' ?>">
          <?= htmlspecialchars($mensaje) ?>
        </div>
      <?php endif; ?>

      <div class="perfil-content">
        <div class="perfil-sidebar">
          <div class="perfil-avatar">
            <?php if (!empty($perfil['FotoPerfilPath']) && file_exists(__DIR__ . '/../../public/' . $perfil['FotoPerfilPath'])): ?>
            <img src="<?= URLROOT ?>/<?= htmlspecialchars($perfil['FotoPerfilPath']) ?>" 
                 alt="Foto de perfil" class="avatar-image">
        <?php else: ?>
            <div class="avatar-circle">
                <?= strtoupper(substr($nombreSesion, 0, 1)) ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="perfil-info">
        <h2><?= htmlspecialchars($nombreSesion) ?></h2>
        <div class="email">
            <?= $perfil && $perfil['EmpleoDeseado'] ? htmlspecialchars($perfil['EmpleoDeseado']) : 'Candidato' ?>
        </div>
    </div>

    <?php if ($perfil): ?>
        <div class="perfil-stats">
            <?php if ($perfil['Cedula']): ?>
                <div class="stat-item">
                    <span class="stat-label">Cédula</span>
                    <span class="stat-value"><?= htmlspecialchars($perfil['Cedula']) ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($edadCalculada): ?>
                <div class="stat-item">
                    <span class="stat-label">Edad</span>
                    <span class="stat-value"><?= $edadCalculada ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($perfil['EstadoCivil']): ?>
                <div class="stat-item">
                    <span class="stat-label">Estado Civil</span>
                    <span class="stat-value"><?= htmlspecialchars($perfil['EstadoCivil']) ?></span>
                </div>
            <?php endif; ?>
            
            <div class="stat-item">
                <span class="stat-label">Solicitudes</span>
                <span class="stat-value"><?= $totalSolicitudes ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($perfil['HojaDeVidaPath'])): ?>
        <a href="<?= URLROOT ?>/Upload/serveFile?file=<?= urlencode($perfil['HojaDeVidaPath']) ?>" target="_blank" class="cv-link">
            📄 Ver Hoja de Vida
        </a>
    <?php endif; ?>
</div>

        <div class="perfil-form">
          <div class="form-header">
            <h2><?= $esNuevoPerfil ? 'Crear Perfil' : 'Actualizar Información' ?></h2>
          </div>
          
          <div class="form-content">
            <?php if (!$esNuevoPerfil): ?>
              <div class="blocked-info">
                <strong>ℹ️ Información:</strong> Algunos campos como nombre, fecha de nacimiento y cédula no se pueden modificar después de crear el perfil.
              </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" action="<?= URLROOT ?>/Candidato/perfil">
              <div class="form-section">
                <h3 class="section-title">
                  👤 Información Personal
                </h3>
                
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label" for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" class="form-input" 
                           value="<?= htmlspecialchars($nombreSesion) ?>" 
                           <?= $bloquearNombre ? 'readonly' : '' ?>>
                    <div class="form-help">Este nombre se toma de tu sesión actual</div>
                  </div>
                  
                  <div class="form-group">
        <label class="form-label" for="edad">Fecha de Nacimiento *</label>
        <input type="date" id="edad" name="edad" class="form-input" 
               value="<?= htmlspecialchars($perfil['Edad'] ?? '') ?>" 
               <?= $bloquearCamposBasicos ? 'readonly' : '' ?> required>
        <?php if (!$bloquearCamposBasicos): ?>
            <div class="form-help">Debes ser mayor de 18 años</div>
        <?php endif; ?>
    </div>
    
                </div>
                
                <div class="form-row">
                  <div class="form-group">
        <label class="form-label" for="cedula">Cédula de Identidad *</label>
        <input type="text" id="cedula" name="cedula" 
               class="form-input <?= $errorCedula ? 'error-field' : '' ?>" 
               value="<?= htmlspecialchars($perfil['Cedula'] ?? $cedulaOriginal ?? '') ?>" 
               <?= $bloquearCamposBasicos ? 'readonly' : '' ?> required>
        <?php if ($errorCedula): ?>
            <div class="error-msg"><?= $errorCedula ?></div>
        <?php endif; ?>
        <?php if (!$bloquearCamposBasicos && $cedulaOriginal): ?>
            <div class="form-help">Debe coincidir con tu cédula de registro: <?= $cedulaOriginal ?></div>
        <?php endif; ?>
    </div>
                  
                  <div class="form-group">
                    <label class="form-label" for="estado_civil">Estado Civil</label>
                    <input type="text" id="estado_civil" name="estado_civil" class="form-input" 
                           value="<?= htmlspecialchars($perfil['EstadoCivil'] ?? '') ?>"
                           placeholder="Ej: Soltero, Casado, Unión libre">
                  </div>
                </div>
              </div>

<!-- Campos básicos con bloqueo condicional -->


              <div class="form-section">
                <h3 class="section-title">
                  📞 Información de Contacto
                </h3>
                
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label" for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" class="form-input" 
                           value="<?= htmlspecialchars($perfil['Telefono'] ?? '') ?>"
                           placeholder="Ej: +57 300 123 4567">
                  </div>
                  
                  <div class="form-group">
                    <label class="form-label" for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" class="form-input" 
                           value="<?= htmlspecialchars($perfil['Direccion'] ?? '') ?>"
                           placeholder="Ej: Carrera 10 #20-30, Villavicencio">
                  </div>
                </div>
              </div>

              <div class="form-section">
                <h3 class="section-title">
                  💼 Información Profesional
                </h3>
                
                <div class="form-group">
                  <label class="form-label" for="empleo_deseado">Empleo Deseado</label>
                  <input type="text" id="empleo_deseado" name="empleo_deseado" class="form-input" 
                         value="<?= htmlspecialchars($perfil['EmpleoDeseado'] ?? '') ?>"
                         placeholder="Ej: Desarrollador Web, Contador, Asistente Administrativo">
                </div>

                <div class="form-group">
                  <label class="form-label" for="descripcion">Descripción General (Estudios y Experiencia)</label>
                  <textarea id="descripcion" name="descripcion" class="form-input" rows="6" 
                            placeholder="Describe tu experiencia laboral, educación, habilidades y logros más relevantes..."><?= htmlspecialchars($perfil['Descripcion'] ?? '') ?></textarea>
                  <div class="form-help">Incluye información sobre tus estudios, experiencia laboral y habilidades principales</div>
                </div>
              </div>

              <div class="form-section">
                <h3 class="section-title">
                  📄 Hoja de Vida
                </h3>
                
                <div class="form-group">
                  <label class="form-label" for="cv">Subir Hoja de Vida (PDF máximo 5MB)</label>
                  <input type="file" id="cv" name="cv" class="file-input" accept="application/pdf">
                  <div class="form-help">
                    Sube tu hoja de vida en formato PDF. El archivo anterior será reemplazado si subes uno nuevo.
                  </div>
                </div>
              </div>

              <button type="submit" class="btn-actualizar">
                💾 <?= $esNuevoPerfil ? 'Crear Perfil' : 'Actualizar Perfil' ?>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>