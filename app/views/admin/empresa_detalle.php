<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= ucfirst($accion) ?> Empresa</title>

  <!-- Asegúrate de que la ruta sea correcta -->
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/dashboard_admin.css?v=<?= time() ?>">

  <link rel="icon" href="<?= URLROOT ?>/public/images/imagesolologo.png" type="image/png">
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2><a href="<?= URLROOT ?>" class="logo-link">WorkFinderPro</a></h2>
    <ul>
      <li><a href="<?= URLROOT ?>/Admin/dashboard">Inicio</a></li>
      <li><a href="<?= URLROOT ?>/Admin/gestionarEmpresas">Gestionar Empresas</a></li>
      <li><a href="<?= URLROOT ?>/Admin/gestionarCandidatos">Gestionar Candidatos</a></li>
      <li><a href="<?= URLROOT ?>/Admin/estadisticas">Estadísticas</a></li>
      <li><a href="<?= URLROOT ?>/Login/logout">Cerrar Sesión</a></li>
    </ul>
  </div>

  <!-- Contenido -->
  <div class="main">
    <div class="header">
      <h1><?= ucfirst($accion) ?> Empresa</h1>
    </div>

    <section>
      <?php if ($accion == 'editar'): ?>
  <form method="POST" action="<?= URLROOT ?>/Admin/editarEmpresa/<?= $empresa['IdUsuario'] ?>" class="form-editar">
    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?= htmlspecialchars($empresa['Nombre']) ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($empresa['Email']) ?>" required>



    <button type="submit" class="btn-guardar">💾 Guardar Cambios</button>
    <a href="<?= URLROOT ?>/Admin/gestionarEmpresas" class="btn-cancelar">Cancelar</a>
  </form>
<?php endif; ?>

    </section>
  </div>
</body>
</html>
