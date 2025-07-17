<?php

session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'Empresa') {
    header("Location: login.php");
    exit();
}


require 'auth_guard.php';
require_once(__DIR__ . '/conexion.php');

if ($_SESSION['usuario_rol'] != 3) {
    header('Location: login.php');
    exit;
}

$idEmpresa = $_SESSION['usuario_id'];
$idUsuario = $_POST['id_usuario'] ?? null;
$idPuesto = $_POST['id_puesto'] ?? null;

if (!$idUsuario || !$idPuesto) {
    die("Faltan datos.");
}

$stmt = $pdo->prepare("INSERT INTO invitaciones (IdEmpresa, IdUsuario, IdPuestoTrabajo) VALUES (?, ?, ?)");
$stmt->execute([$idEmpresa, $idUsuario, $idPuesto]);

header("Location: invitar_candidatos.php?msg=Invitación enviada");
exit;

