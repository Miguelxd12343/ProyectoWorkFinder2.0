<?php
require_once(__DIR__ . '/conexion.php');
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header('Location: login.php');
    exit;
}

// Procesar eliminación o cierre
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idOferta = $_POST['id_oferta'] ?? null;
    $accion = $_POST['accion'] ?? null;

    if ($idOferta && $accion) {
        // Verificar que la oferta le pertenezca a la empresa
        $stmtCheck = $pdo->prepare("SELECT IdUsuario FROM puestodetrabajo WHERE IdPuesto = ?");
        $stmtCheck->execute([$idOferta]);
        $oferta = $stmtCheck->fetch();

        if ($oferta && $oferta['IdUsuario'] == $_SESSION['usuario_id']) {
            if ($accion === 'eliminar') {
                $stmtDelete = $pdo->prepare("DELETE FROM puestodetrabajo WHERE IdPuesto = ?");
                $stmtDelete->execute([$idOferta]);
            } elseif ($accion === 'cerrar') {
                $stmtCerrar = $pdo->prepare("UPDATE puestodetrabajo SET Estado = 'Inactiva' WHERE IdPuesto = ?");
                $stmtCerrar->execute([$idOferta]);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Ofertas Publicadas</title>
    <link rel="stylesheet" href="../CSS/ver_ofertas_empresa.css">
</head>
<body>
    <div class="container">
        <h2>Ofertas Publicadas</h2>

        <?php
        try {
            $stmt = $pdo->query("SELECT pt.IdPuesto, pt.Titulo, pt.Descripcion, pt.Ubicacion, pt.TipoContrato, pt.FechaPublicacion, pt.Estado, pt.IdUsuario, u.Nombre AS Empresa
                                 FROM puestodetrabajo pt
                                 INNER JOIN usuario u ON pt.IdUsuario = u.IdUsuario
                                 ORDER BY pt.FechaPublicacion DESC");

            while ($oferta = $stmt->fetch()) {
                echo "<div class='oferta'>";
                echo "<h3>" . htmlspecialchars($oferta['Titulo']) . "</h3>";
                echo "<p><strong>Empresa:</strong> " . htmlspecialchars($oferta['Empresa']) . "</p>";
                echo "<p><strong>Ubicación:</strong> " . htmlspecialchars($oferta['Ubicacion']) . "</p>";
                echo "<p><strong>Contrato:</strong> " . htmlspecialchars($oferta['TipoContrato']) . "</p>";
                echo "<p><strong>Estado:</strong> " . htmlspecialchars($oferta['Estado']) . "</p>";
                echo "<p><strong>Fecha:</strong> " . htmlspecialchars($oferta['FechaPublicacion']) . "</p>";
                echo "<p>" . nl2br(htmlspecialchars($oferta['Descripcion'])) . "</p>";

                // Mostrar botones si la oferta le pertenece al usuario actual
                if ($oferta['IdUsuario'] == $_SESSION['usuario_id']) {
                    echo "<form method='POST' class='acciones'>";
                    echo "<input type='hidden' name='id_oferta' value='" . $oferta['IdPuesto'] . "' />";
                    echo "<button type='submit' name='accion' value='cerrar' class='cerrar'>Cerrar</button>";
                    echo "<button type='submit' name='accion' value='eliminar' class='eliminar' onclick='return confirm(\"¿Seguro que deseas eliminar esta oferta?\")'>Eliminar</button>";
                    echo "</form>";
                }

                echo "</div>";
            }
        } catch (PDOException $e) {
            echo "<p>Error al cargar las ofertas: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
</body>
</html>

