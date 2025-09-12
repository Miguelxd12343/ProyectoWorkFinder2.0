<!DOCTYPE html> 
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Administrador</title>
    <link rel="stylesheet" href="../public/css/dashboard_admin.css?v=<?php echo time(); ?>">


    <link rel="icon" href="public/images/imagesolologo.png" type="image/png">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

  <!-- Contenido Principal -->
  <div class="main">
    <div class="header">
      <h1>Panel de Administrador</h1>
      <p>Bienvenido, <strong><?= $_SESSION['usuario_nombre'] ?? 'Administrador' ?></strong></p>
    </div>

    <!-- Estadísticas -->
    <section id="estadisticas">
      <h2>Estadísticas del Sistema</h2>
      <canvas id="graficoUsuarios" height="100"></canvas>
      <script>
        fetch('<?= URLROOT ?>/Admin/datosEstadisticas')
          .then(res => res.json())
          .then(data => {
            const labels = data.map(item => item.Nombre);
            const values = data.map(item => item.IdUsuario);
            new Chart(document.getElementById('graficoUsuarios'), {
              type: 'bar',
              data: {
                labels: labels,
                datasets: [{
                  label: 'Usuarios',
                  data: values,
                  backgroundColor: '#0073b1'
                }]
              }
            });
          });
      </script>
    </section>


  </div>
</body>
</html>
