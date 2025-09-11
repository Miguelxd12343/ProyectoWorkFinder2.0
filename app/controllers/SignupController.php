<?php
require_once __DIR__ . '/../../libraries/Database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../../libraries/MailService.php';

class SignupController {
    private $usuario;

    public function __construct() {
        $pdo = (new Database())->getConnection();
        $this->usuario = new Usuario($pdo);
    }


    public function mostrarFormulario() {
        $error = $_GET['error'] ?? null;
        require_once __DIR__ . '/../views/registro/form.php';
    }

    
    public function registrarUsuario(): void {
        try {
        // Validar datos obligatorios
        if (empty($_POST['nombre']) || empty($_POST['email']) || 
            empty($_POST['contrasena']) || empty($_POST['rol'])) {
            throw new Exception("Todos los campos son obligatorios.");
        }

        // Validar contraseñas iguales
        if ($_POST['contrasena'] !== $_POST['confirm_password']) {
            throw new Exception("Las contraseñas no coinciden.");
        }

        $data = $_POST;
        $resultado = $this->usuario->registrar($data);

        if ($resultado) {
            // ✅ Enviar correo de bienvenida
            $mailService = new MailService();
            $mailService->enviarBienvenida($data['email'], $data['nombre'], 2); // 2 = candidato

            require_once __DIR__ . '/../views/registro/RegistroExitoso.php';
            exit;
        } else {
            throw new Exception("No se pudo registrar el usuario.");
        }

    } catch (Exception $e) {
        header("Location: " . URLROOT . "/Signup/mostrarFormulario?error=" . urlencode($e->getMessage()));
        exit;
        }
    }
}
?>