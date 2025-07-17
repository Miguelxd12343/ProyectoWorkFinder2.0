<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header("Location: login.php");
    exit();
}

require_once(__DIR__ . '/conexion.php');

$nombreEmpresa = $_SESSION['usuario_nombre'];
$idEmpresa = $_SESSION['usuario_id'];

// Obtener candidatos
$sql = "SELECT u.IdUsuario, u.Nombre, p.Edad, p.EmpleoDeseado, p.Descripcion
        FROM usuario u
        JOIN perfilusuario p ON u.IdUsuario = p.IdUsuario
        WHERE u.IdRol = 2";
$stmt = $pdo->query($sql);
$candidatos = $stmt->fetchAll();

// Obtener ofertas de esta empresa
$sql2 = "SELECT IdPuesto, Titulo FROM puestodetrabajo WHERE IdUsuario = ?";
$stmt2 = $pdo->prepare($sql2);
$stmt2->execute([$idEmpresa]);
$puestos = $stmt2->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Invitar Candidatos - WorkFinderPro</title>
    <link rel="stylesheet" href="../CSS/dashboard_empresa.css">
    <link rel="stylesheet" href="../CSS/invitar_candidatos.css">
</head>
<body>
    <div class="sidebar">
        <h2><a href="../index.html" class="logo-link">WorkFinderPro</a></h2>
        <ul>
            <li><a href="dashboard_empresa.php">Inicio</a></li>
            <li><a href="crear_oferta.php">Crear Oferta</a></li>
            <li><a href="ver_ofertas_empresa.php">Ver Ofertas Publicadas</a></li>
            <li class="active"><a href="invitar_candidatos.php">Invitar Candidatos</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <h1>Bienvenido, <?= htmlspecialchars($nombreEmpresa) ?></h1>
            <p>Aquí puedes invitar candidatos a tus ofertas de empleo.</p>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="success-message"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <div class="cards">
            <?php foreach ($candidatos as $c): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($c['Nombre']) ?></h3>
                    <p><strong>Edad:</strong> <?= htmlspecialchars($c['Edad']) ?></p>
                    <p><strong>Empleo Deseado:</strong> <?= htmlspecialchars($c['EmpleoDeseado']) ?></p>
                    <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($c['Descripcion'])) ?></p>

                    <form method="POST" action="invitar_candidato.php">
                        <input type="hidden" name="IdCandidato" value="<?= $c['IdUsuario'] ?>">
                        <label>Selecciona una oferta:</label>
                        <select name="IdPuesto" required>
                            <option value="">-- Seleccionar --</option>
                            <?php foreach ($puestos as $p): ?>
                                <option value="<?= $p['IdPuesto'] ?>"><?= htmlspecialchars($p['Titulo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label>Mensaje (opcional):</label>
                        <textarea name="Mensaje" placeholder="Mensaje personalizado..."></textarea>
                        <button type="submit">Invitar</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
