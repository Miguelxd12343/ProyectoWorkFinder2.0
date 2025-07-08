<?php
require_once(__DIR__ . '/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? null;
    $email = $_POST['email'] ?? null;
    $contrasena = isset($_POST['contrasena']) ? password_hash($_POST['contrasena'], PASSWORD_DEFAULT) : null;
    $rol = $_POST['rol'] ?? null;

    // Campos adicionales para empresa
    $direccionEmpresa = $_POST['direccion_empresa'] ?? null;
    $identificacionFiscal = $_POST['identificacion_fiscal'] ?? null;

    if ($nombre && $email && $contrasena && $rol) {
        try {
            if ($rol == '1') { // Empresa
                $stmt = $pdo->prepare("INSERT INTO usuario (Nombre, Email, Contrasena, IdRol, DireccionEmpresa, IdentificacionFiscal)
                                       VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $email, $contrasena, $rol, $direccionEmpresa, $identificacionFiscal]);
            } else { // Candidato
                $stmt = $pdo->prepare("INSERT INTO usuario (Nombre, Email, Contrasena, IdRol)
                                       VALUES (?, ?, ?, ?)");
                $stmt->execute([$nombre, $email, $contrasena, $rol]);
            }


            
            header("Location: ../HTML/RegistroExitoso.html");
            exit;

        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                echo "Error: El correo ya está registrado.";
            } else {
                echo "Error en el registro: " . $e->getMessage();
            }
        }
    } else {
        echo "Faltan datos del formulario.";
    }
} else {
    echo "Acceso no permitido.";

}

