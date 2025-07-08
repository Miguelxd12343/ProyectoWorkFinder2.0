<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT idUsuario FROM usuario WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("UPDATE usuario SET Contrasena = ?, reset_token = NULL, token_expiry = NULL WHERE idUsuario = ?");
        $stmt->execute([$password, $user['idUsuario']]);
        echo "Contraseña actualizada correctamente.";
    } else {
        echo "Token inválido o expirado.";
    }
}
?>