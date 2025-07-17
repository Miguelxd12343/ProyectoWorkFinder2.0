<?php
require_once(__DIR__ . '/conexion.php');
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 2) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_puesto'])) {
    $idUsuario = $_SESSION['usuario_id'];
    $idPuesto = $_POST['id_puesto'];

    // Verificar si ya se postuló antes
    $verificar = $pdo->prepare("SELECT COUNT(*) FROM solicitudempleo WHERE IdUsuario = ? AND IdPuesto = ?");
    $verificar->execute([$idUsuario, $idPuesto]);

    if ($verificar->fetchColumn() > 0) {
        header('Location: dashboard_usuario.php?msg=Ya te has postulado a esta oferta.');
        exit;
    }

    // Insertar nueva postulación
    $stmt = $pdo->prepare("INSERT INTO solicitudempleo (IdUsuario, IdPuesto, FechaSolicitud) VALUES (?, ?, NOW())");
    $stmt->execute([$idUsuario, $idPuesto]);

    header('Location: dashboard_usuario.php?msg=Postulación enviada con éxito.');
    exit;
} else {
    header('Location: dashboard_usuario.php?msg=Solicitud inválida.');
    exit;
}
?>