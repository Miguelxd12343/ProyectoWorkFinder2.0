<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../../libraries/Database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario esté logueado y sea candidato (rol 2)
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 2) {
    header("Location: " . URLROOT . "/Login/index");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_puesto'])) {
    $pdo = (new Database())->getConnection();

    $idUsuario = $_SESSION['usuario_id'];
    $idPuesto  = $_POST['id_puesto'];

    try {
        // Verificar si ya existe postulación
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM solicitudempleo WHERE IdUsuario = ? AND IdPuesto = ?");
        $stmt->execute([$idUsuario, $idPuesto]);

        if ($stmt->fetchColumn() > 0) {
            header("Location: " . URLROOT . "/Candidato/ofertas?msg=" . urlencode("Ya te has postulado a esta oferta."));
            exit;
        }

        // Insertar la postulación
        $stmt = $pdo->prepare("INSERT INTO solicitudempleo (IdUsuario, IdPuesto, FechaEnvio) VALUES (?, ?, NOW())");
        $stmt->execute([$idUsuario, $idPuesto]);

        header("Location: " . URLROOT . "/Candidato/ofertas?msg=" . urlencode("¡Postulación enviada con éxito! 🚀"));
        exit;

    } catch (PDOException $e) {
        header("Location: " . URLROOT . "/Candidato/ofertas?msg=" . urlencode("Error al postular: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: " . URLROOT . "/Candidato/ofertas?msg=" . urlencode("Solicitud inválida"));
    exit;
}
