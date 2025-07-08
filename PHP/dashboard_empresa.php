<?php
require_once(__DIR__ . '/conexion.php');
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header('Location: login.php');
    exit;
}

$nombreEmpresa = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Empresa - WorkFinderPro</title>
    <link rel="stylesheet" href="../CSS/dashboard_empresa.css">
</head>
<body>
    <div class="sidebar">
        <h2>WorkFinderPro</h2>
        <ul>
            <li class="active"><a href="#">Inicio</a></li>
            <li><a href="crear_oferta.php">Crear Oferta</a></li>
            <li><a href="ver_ofertas_empresa.php">Ver Ofertas Publicadas</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <h1>Bienvenido, <?= htmlspecialchars($nombreEmpresa) ?></h1>
            <p>Este es tu panel de empresa para gestionar ofertas de empleo.</p>
        </div>

        <div class="cards">
            <div class="card">
                <h3>¿Deseas publicar una oferta?</h3>
                <a href="crear_oferta.php" class="btn">Crear Oferta</a>
            </div>
            <div class="card">
                <h3>Ver Ofertas Publicadas</h3>
                <a href="ver_ofertas_empresa.php" class="btn">Ver Ofertas</a>
            </div>
        </div>
    </div>
</body>
</html>
