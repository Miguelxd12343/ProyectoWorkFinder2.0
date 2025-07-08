<?php
require_once(__DIR__ . '/conexion.php');
require_once(__DIR__ . '/auth_guard.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsuario = $_SESSION['usuario_id'];
    $idPuesto = $_POST['id_puesto'] ?? null;

    if ($idPuesto) {
        // Verifica si ya se postuló
        $verificar = $pdo->prepare("SELECT COUNT(*) FROM solicitudempleo WHERE IdUsuario = ? AND IdPuestoTrabajo = ?");
        $verificar->execute([$idUsuario, $idPuesto]);
        $yaPostulado = $verificar->fetchColumn();

        if ($yaPostulado > 0) {
            $mensaje = "Ya te has postulado a esta oferta.";
        } else {
            $insertar = $pdo->prepare("INSERT INTO solicitudempleo (FechaEnvio, Estado, IdUsuario, IdPuestoTrabajo) VALUES (NOW(), 'Pendiente', ?, ?)");
            $insertar->execute([$idUsuario, $idPuesto]);
            $mensaje = "¡Postulación enviada correctamente!";
        }
    } else {
        $mensaje = "Oferta no válida.";
    }
} else {
    $mensaje = "Acceso inválido.";
}

// Redirige de nuevo al dashboard con mensaje
header("Location: dashboard_usuario.php?msg=" . urlencode($mensaje));
exit;
?>
