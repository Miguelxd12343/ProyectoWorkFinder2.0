<?php
require 'conexion.php'; // conexión con PDO

$stmt = $pdo->query("SELECT idUsuario, Nombre FROM usuario ORDER BY idUsuario ASC");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);