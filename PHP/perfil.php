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
                } else {
                    $mensaje = "El archivo debe ser PDF y menor de 5MB.";
                }
            }

            // Insertar o actualizar perfil
            if ($esNuevoPerfil) {
                $stmt = $pdo->prepare("INSERT INTO perfilusuario (IdUsuario, Edad, Cedula, EstadoCivil, Telefono, Direccion, EmpleoDeseado, Descripcion, HojaDeVidaPath) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$idUsuario, $fechaNacimiento, $cedula, $estadoCivil, $telefono, $direccion, $empleo, $descripcion, $cvPath]);
                $esNuevoPerfil = false;
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
                $mensaje = "Perfil actualizado.";
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Formulario de Perfil</title>
  <link rel="stylesheet" href="../CSS/styles_perfil.css">
  <style>
    .error-msg { color: red; font-size: 0.9em; margin-top: 5px; }
    .success-msg { color: green; font-weight: bold; margin-top: 10px; }
  </style>
</head>
<body>
  <header class="nav">
    <a href="../index.html">
      <img src="../images/imagesolologo.png" class="nav-logo" alt="Logo">
    </a>
    <nav><a href="../index.html">Inicio</a></nav>
  </header>

  <main class="form-container">
    <form method="POST" enctype="multipart/form-data">
      <div class="left-panel">
        <label>Nombre: <input type="text" name="nombre" value="<?= htmlspecialchars($nombreSesion) ?>" <?= $bloquearNombre ? 'readonly' : '' ?>></label>

        <label>Fecha de nacimiento:
          <input type="date" name="edad" value="<?= htmlspecialchars($perfil['Edad'] ?? '') ?>" <?= $bloquearEdad ? 'readonly' : '' ?> required>
        </label>

        <label>Cédula:
          <input type="text" name="cedula" value="<?= htmlspecialchars($perfil['Cedula'] ?? '') ?>" <?= $bloquearCedula ? 'readonly' : '' ?> required>
          <?php if ($errorCedula): ?><span class="error-msg"><?= $errorCedula ?></span><?php endif; ?>
        </label>

        <label>Estado Civil:
          <input type="text" name="estado_civil" value="<?= htmlspecialchars($perfil['EstadoCivil'] ?? '') ?>">
        </label>

        <label>Teléfono:
          <input type="tel" name="telefono" value="<?= htmlspecialchars($perfil['Telefono'] ?? '') ?>">
        </label>

        <label>Dirección:
          <input type="text" name="direccion" value="<?= htmlspecialchars($perfil['Direccion'] ?? '') ?>">
        </label>

        <label>Empleo deseado:
          <input type="text" name="empleo_deseado" value="<?= htmlspecialchars($perfil['EmpleoDeseado'] ?? '') ?>">
        </label>
      </div>

      <div class="right-panel">
        <label>Descripción General (estudios y experiencia):</label>
        <textarea name="descripcion" rows="10"><?= htmlspecialchars($perfil['Descripcion'] ?? '') ?></textarea>

        <div class="upload-section">
          <label>Subir Hoja de Vida (PDF máx 5MB):</label>
          <input type="file" name="cv" accept="application/pdf">
          <?php if (!empty($perfil['HojaDeVidaPath'])): ?>
            <p><a href="<?= $perfil['HojaDeVidaPath'] ?>" target="_blank">Ver hoja de vida</a></p>
          <?php endif; ?>
        </div>

        <?php if ($mensaje): ?><p class="success-msg"><?= $mensaje ?></p><?php endif; ?>

        <button class="submit-btn" type="submit">GUARDAR</button>
      </div>
    </form>
  </main>
</body>
</html>
