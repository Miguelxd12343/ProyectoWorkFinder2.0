<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Administrador</title>
  <link rel="stylesheet" href="../CSS/dashboard_admin.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
      <li class="active"><a href="#">Inicio</a></li>
      <li><a href="editar_usuario.php">Usuarios</a></li>
      <li><a href="#empresas">Empresas</a></li>
      <li><a href="#estadisticas">Estadísticas</a></li>
      <li><a href="#reportes">Generar Reporte</a></li>
      <li><a href="logout.php">Cerrar Sesión</a></li>
    </ul>
  </div>

  <div class="main">
    <div class="header">
      <h1>Panel de Administrador</h1>
      <p>Bienvenido, <strong>Administrador</strong></p>
    </div>

    <section id="usuarios">
      <h2>Usuarios Registrados</h2>
      <div class="tabla-usuarios">
        <!-- tabla generada con PHP -->
        <?php
        require 'conexion.php';
        $stmt = $pdo->query("SELECT IdUsuario, Nombre, Email FROM usuario WHERE IdRol = 2");
        echo "<table><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Acciones</th></tr>";
        while ($u = $stmt->fetch()) {
          echo "<tr><td>{$u['IdUsuario']}</td><td>{$u['Nombre']}</td><td>{$u['Email']}</td><td>
                <a href='editar_usuario.php?id={$u['IdUsuario']}' class='editar'>Editar</a>
                <a href='eliminar_usuario.php?id={$u['IdUsuario']}' class='eliminar' onclick='return confirm(\"Seguro?\")'>Eliminar</a>
              </td></tr>";
        }
        echo "</table>";
        ?>
      </div>
    </section>

    <section id="empresas">
      <h2>Empresas Registradas</h2>
      <div class="tabla-usuarios">
        <?php
        $stmt = $pdo->query("SELECT IdUsuario, Nombre, Email FROM usuario WHERE IdRol = 1");
        echo "<table><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Acciones</th></tr>";
        while ($e = $stmt->fetch()) {
          echo "<tr><td>{$e['IdUsuario']}</td><td>{$e['Nombre']}</td><td>{$e['Email']}</td><td>
                <a href='editar_usuario.php?id={$e['IdUsuario']}' class='editar'>Editar</a>
                <a href='eliminar_usuario.php?id={$e['IdUsuario']}' class='eliminar' onclick='return confirm(\"Seguro?\")'>Eliminar</a>
              </td></tr>";
        }
        echo "</table>";
        ?>
      </div>
    </section>

    <section id="estadisticas">
      <h2>Estadísticas del Sistema</h2>
      <canvas id="graficoUsuarios" height="100"></canvas>
      <script>
        fetch('../PHP/datos_graficasdashboardperfil.php')
          .then(res => res.json())
          .then(data => {
            const labels = data.map(item => item.Nombre);
            const values = data.map(item => item.idUsuario);
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

    <section id="reportes">
      <h2>Generar Reporte PDF</h2>
      <form action="generar_reporte.php" method="POST">
        <button type="submit">Generar Reporte de Usuarios</button>
      </form>
    </section>
  </div>
</body>
</html>
