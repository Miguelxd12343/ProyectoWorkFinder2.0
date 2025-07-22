<?php
require 'auth_guard.php';
require_once(__DIR__ . '/conexion.php');

$idUsuario = $_SESSION['usuario_id'];
$nombreSesion = $_SESSION['usuario_nombre'] ?? '';
$mensaje = "";
$errorCedula = "";

// Consultar si ya existe un perfil
$stmt = $pdo->prepare("SELECT * FROM perfilusuario WHERE IdUsuario = ?");
$stmt->execute([$idUsuario]);
$perfil = $stmt->fetch();

$esNuevoPerfil = !$perfil;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $nombreSesion; // Solo se toma de la sesión
    $fechaNacimiento = $_POST['edad'] ?? '';
    $cedula = $_POST['cedula'] ?? '';
    $estadoCivil = $_POST['estado_civil'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $empleo = $_POST['empleo_deseado'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $cvPath = $perfil['HojaDeVidaPath'] ?? null;

    // Validación edad >= 18
    $hoy = new DateTime();
    $fechaNac = new DateTime($fechaNacimiento);
    $edad = $fechaNac->diff($hoy)->y;

    if ($edad < 18) {
        $mensaje = "Debes ser mayor de edad.";
        $tipo_mensaje = 'error';
    } else {
        // Validar duplicado de cédula
        $stmt = $pdo->prepare("SELECT * FROM perfilusuario WHERE Cedula = ? AND IdUsuario != ?");
        $stmt->execute([$cedula, $idUsuario]);
        if ($stmt->fetch()) {
            $errorCedula = "La cédula ya está registrada.";
        } else {
            // Subir PDF si corresponde
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                if ($_FILES['cv']['size'] <= 5 * 1024 * 1024 && mime_content_type($_FILES['cv']['tmp_name']) === 'application/pdf') {
                    $nombreArchivo = "hojadevida.pdf";
                    $carpetaUsuario = __DIR__ . "/uploads/$cedula/";
                    if (!is_dir($carpetaUsuario)) mkdir($carpetaUsuario, 0777, true);
                    $rutaCompleta = $carpetaUsuario . $nombreArchivo;
                    move_uploaded_file($_FILES['cv']['tmp_name'], $rutaCompleta);
                    $cvPath = "uploads/$cedula/$nombreArchivo";
                    $mensaje = "Perfil guardado con éxito.";
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = "El archivo debe ser PDF y menor de 5MB.";
                    $tipo_mensaje = 'error';
                }
            }

            // Insertar o actualizar perfil
            if ($esNuevoPerfil) {
                $stmt = $pdo->prepare("INSERT INTO perfilusuario (IdUsuario, Edad, Cedula, EstadoCivil, Telefono, Direccion, EmpleoDeseado, Descripcion, HojaDeVidaPath) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$idUsuario, $fechaNacimiento, $cedula, $estadoCivil, $telefono, $direccion, $empleo, $descripcion, $cvPath]);
                $esNuevoPerfil = false;
                if (!$mensaje) {
                    $mensaje = "Perfil creado con éxito.";
                    $tipo_mensaje = 'success';
                }
            } else {
                $query = "UPDATE perfilusuario SET EstadoCivil=?, Telefono=?, Direccion=?, EmpleoDeseado=?, Descripcion=?";
                $params = [$estadoCivil, $telefono, $direccion, $empleo, $descripcion];
                if ($cvPath) {
                    $query .= ", HojaDeVidaPath=?";
                    $params[] = $cvPath;
                }
                $query .= " WHERE IdUsuario=?";
                $params[] = $idUsuario;
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                if (!$mensaje) {
                    $mensaje = "Perfil actualizado.";
                    $tipo_mensaje = 'success';
                }
            }

            // Recargar perfil actualizado
            $stmt = $pdo->prepare("SELECT * FROM perfilusuario WHERE IdUsuario = ?");
            $stmt->execute([$idUsuario]);
            $perfil = $stmt->fetch();
        }
    }
}

// Determinar campos bloqueados
$bloquearNombre = true;
$bloquearEdad = !$esNuevoPerfil;
$bloquearCedula = !$esNuevoPerfil;

// Obtener estadísticas básicas
$stmt_solicitudes = $pdo->prepare("SELECT COUNT(*) as total FROM solicitudempleo WHERE IdUsuario = ?");
$stmt_solicitudes->execute([$idUsuario]);
$totalSolicitudes = $stmt_solicitudes->fetchColumn();

// Calcular edad si existe fecha de nacimiento
$edadCalculada = '';
if ($perfil && $perfil['Edad']) {
    try {
        $fechaNac = new DateTime($perfil['Edad']);
        $hoy = new DateTime();
        $edadCalculada = $fechaNac->diff($hoy)->y . ' años';
    } catch (Exception $e) {
        $edadCalculada = '';
    }
}
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
  <link rel="stylesheet" href="../CSS/dashboard_usuario.css" />
  
  <style>
    .perfil-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    .perfil-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .perfil-header h1 {
      margin: 0;
      font-size: 2.5em;
      font-weight: 300;
    }

    .perfil-header p {
      margin: 10px 0 0 0;
      opacity: 0.9;
      font-size: 1.1em;
    }

    .mensaje {
      padding: 15px 20px;
      margin-bottom: 20px;
      border-radius: 8px;
      font-weight: 500;
      animation: slideIn 0.3s ease-out;
    }

    .mensaje.success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .mensaje.error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .perfil-content {
      display: grid;
      grid-template-columns: 1fr 2fr;
      gap: 30px;
    }

    .perfil-sidebar {
      background: white;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
      padding: 30px;
      height: fit-content;
      position: sticky;
      top: 20px;
    }

    .perfil-avatar {
      text-align: center;
      margin-bottom: 25px;
    }

    .avatar-circle {
      width: 120px;
      height: 120px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3em;
      color: white;
      margin: 0 auto 15px;
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .perfil-info {
      text-align: center;
    }

    .perfil-info h2 {
      color: #333;
      margin: 0 0 5px 0;
      font-size: 1.5em;
      font-weight: 600;
    }

    .perfil-info .email {
      color: #666;
      margin-bottom: 15px;
      font-size: 1em;
    }

    .perfil-stats {
      border-top: 1px solid #eee;
      margin-top: 20px;
      padding-top: 20px;
    }

    .stat-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
      padding: 8px 0;
    }

    .stat-label {
      color: #666;
      font-weight: 500;
    }

    .stat-value {
      color: #333;
      font-weight: 600;
      background: #f8f9ff;
      padding: 4px 10px;
      border-radius: 15px;
      font-size: 0.9em;
    }

    .cv-link {
      display: block;
      margin-top: 15px;
      text-align: center;
      color: #667eea;
      text-decoration: none;
      font-weight: 500;
      padding: 10px;
      background: #f8f9ff;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .cv-link:hover {
      background: #667eea;
      color: white;
      text-decoration: none;
    }

    .perfil-form {
      background: white;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .form-header {
      background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 25px 30px;
    }

    .form-header h2 {
      margin: 0;
      font-size: 1.8em;
      font-weight: 400;
    }

    .form-content {
      padding: 30px;
    }

    .form-section {
      margin-bottom: 30px;
    }

    .section-title {
      color: #333;
      font-size: 1.3em;
      font-weight: 600;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid #f0f0f0;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group.full {
      grid-column: 1 / -1;
    }

    .form-label {
      display: block;
      color: #555;
      font-weight: 600;
      margin-bottom: 8px;
      font-size: 0.95em;
    }

    .form-input {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #e1e5e9;
      border-radius: 8px;
      font-size: 1em;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
      box-sizing: border-box;
    }

    .form-input:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-input:read-only {
      background-color: #f8f9fa;
      color: #6c757d;
    }

    textarea.form-input {
      resize: vertical;
      min-height: 120px;
      font-family: inherit;
    }

    .file-input-wrapper {
      position: relative;
      display: inline-block;
      width: 100%;
    }

    .file-input {
      width: 100%;
      padding: 12px 15px;
      border: 2px dashed #e1e5e9;
      border-radius: 8px;
      background: #f8f9ff;
      font-size: 1em;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .file-input:hover {
      border-color: #667eea;
      background: #f0f2ff;
    }

    .btn-actualizar {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 15px 40px;
      border: none;
      border-radius: 25px;
      font-size: 1.1em;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 10px;
    }

    .btn-actualizar:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-actualizar:active {
      transform: translateY(0);
    }

    .form-help {
      color: #888;
      font-size: 0.9em;
      margin-top: 5px;
      line-height: 1.4;
    }

    .error-field {
      border-color: #e74c3c !important;
    }

    .error-msg {
      color: #e74c3c;
      font-size: 0.9em;
      margin-top: 5px;
      font-weight: 500;
    }

    .blocked-info {
      background: #fff3cd;
      color: #856404;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      border: 1px solid #ffeaa7;
    }

    @media (max-width: 968px) {
      .perfil-content {
        grid-template-columns: 1fr;
      }
      
      .perfil-sidebar {
        position: relative;
        top: auto;
      }
      
      .form-row {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .perfil-container {
        padding: 15px;
      }
      
      .perfil-header {
        padding: 20px;
      }
      
      .perfil-header h1 {
        font-size: 2em;
      }
      
      .form-content {
        padding: 20px;
      }
    }
  </style>

  <script>
    window.addEventListener("pageshow", function (event) {
      if (event.persisted || (performance.getEntriesByType("navigation")[0]?.type === "back_forward")) {
        window.location.reload();
      }
    });

    // Auto-hide mensaje después de 5 segundos
    document.addEventListener('DOMContentLoaded', function() {
      const mensaje = document.querySelector('.mensaje');
      if (mensaje && mensaje.classList.contains('success')) {
        setTimeout(() => {
          mensaje.style.opacity = '0';
          mensaje.style.transform = 'translateY(-10px)';
          setTimeout(() => mensaje.remove(), 300);
        }, 5000);
      }
    });
  </script>
</head>
<body>
  <div class="sidebar">
    <h2><a href="../index.html" class="logo-link">WorkFinderPro</a></h2>
    <ul>
      <li><a href="dashboard_usuario.php">Inicio</a></li>
      <li class="active"><a href="perfil.php">Perfil</a></li>
      <li><a href="ver_ofertas.php">Ofertas</a></li>
      <li><a href="solicitudes.php">Solicitudes</a></li>
      <li><a href="logout.php">Cerrar sesión</a></li>
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
            <div class="avatar-circle">
              <?= strtoupper(substr($nombreSesion, 0, 1)) ?>
            </div>
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
            <a href="<?= htmlspecialchars($perfil['HojaDeVidaPath']) ?>" target="_blank" class="cv-link">
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

            <form method="POST" enctype="multipart/form-data">
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
                           <?= $bloquearEdad ? 'readonly' : '' ?> required>
                    <div class="form-help">Debes ser mayor de 18 años</div>
                  </div>
                </div>
                
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label" for="cedula">Cédula de Identidad *</label>
                    <input type="text" id="cedula" name="cedula" 
                           class="form-input <?= $errorCedula ? 'error-field' : '' ?>" 
                           value="<?= htmlspecialchars($perfil['Cedula'] ?? '') ?>" 
                           <?= $bloquearCedula ? 'readonly' : '' ?> required>
                    <?php if ($errorCedula): ?>
                      <div class="error-msg"><?= $errorCedula ?></div>
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