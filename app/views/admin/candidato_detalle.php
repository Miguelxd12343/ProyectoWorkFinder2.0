<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Candidato</title>
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/dashboard_admin.css?v=<?= time() ?>">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/styles_Detallescandidato.css?v=<?= time() ?>">
  <link rel="icon" href="<?= URLROOT ?>/public/images/imagesolologo.png" type="image/png">
</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2><a href="<?= URLROOT ?>" class="logo-link">WorkFinderPro</a></h2>
    <ul>
      <li><a href="<?= URLROOT ?>/Admin/dashboard">Inicio</a></li>
      <li><a href="<?= URLROOT ?>/Admin/gestionarEmpresas">Gestionar Empresas</a></li>
      <li><a href="<?= URLROOT ?>/Admin/gestionarCandidatos" class="active">Gestionar Candidatos</a></li>
      <li><a href="<?= URLROOT ?>/Admin/estadisticas">Estadísticas</a></li>
      <li><a href="<?= URLROOT ?>/Login/logout">Cerrar Sesión</a></li>
    </ul>
  </div>

  <!-- Contenido -->
  <div class="main">
    <div class="header">
      <h1>Editar Candidato</h1>
      <p>Modifica los datos del candidato seleccionado</p>
    </div>

    <section>
      <form method="POST" enctype="multipart/form-data" action="<?= URLROOT ?>/Admin/editarCandidato/<?= $candidato['IdUsuario'] ?>" class="form-editar">

        <!-- Información básica -->
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($candidato['Nombre']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($candidato['Email']) ?>" required>

        <label>Fecha de Nacimiento:</label>
        <input type="date" name="edad" value="<?= htmlspecialchars($candidato['Edad'] ?? '') ?>">

        <label>Cédula:</label>
        <input type="text" name="cedula" value="<?= htmlspecialchars($candidato['Cedula'] ?? '') ?>">

        <label>Estado Civil:</label>
        <input type="text" name="estado_civil" value="<?= htmlspecialchars($candidato['EstadoCivil'] ?? '') ?>">

        <!-- Contacto -->
        <label>Teléfono:</label>
        <input type="text" name="telefono" value="<?= htmlspecialchars($candidato['Telefono'] ?? '') ?>">

        <label>Dirección:</label>
        <input type="text" name="direccion" value="<?= htmlspecialchars($candidato['Direccion'] ?? '') ?>">

        <!-- Profesional -->
        <label>Empleo Deseado / Cargo:</label>
        <input type="text" name="empleo_deseado" value="<?= htmlspecialchars($candidato['EmpleoDeseado'] ?? '') ?>">

        <label>Descripción General:</label>
        <textarea name="descripcion" rows="5"><?= htmlspecialchars($candidato['Descripcion'] ?? '') ?></textarea>

        <!-- CV -->
        <label>Hoja de Vida (CV):</label>
        <?php if (!empty($candidato['HojaDeVidaPath'])): ?>
          <p>
            <a href="<?= URLROOT ?>/Upload/serveFile?file=<?= urlencode($candidato['HojaDeVidaPath']) ?>" target="_blank">
              📄 Ver CV actual
            </a>
          </p>
        <?php endif; ?>
        <input type="file" name="cv" accept="application/pdf">

        <!-- Botones -->
        <button type="submit" class="btn-guardar">💾 Guardar Cambios</button>
        <a href="<?= URLROOT ?>/Admin/gestionarCandidatos" class="btn-cancelar">Cancelar</a>
      </form>
    </section>
  </div>
</body>
</html>
