<?php
require_once __DIR__ . '/../../libraries/Database.php';
require_once __DIR__ . '/../models/Usuario.php';

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
            // Validar que vengan los datos necesarios
            if (empty($_POST['nombre']) || empty($_POST['email']) || 
                empty($_POST['contrasena']) || empty($_POST['rol'])) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            // Validar que las contraseñas coincidan
            if ($_POST['contrasena'] !== $_POST['confirm_password']) {
                throw new Exception("Las contraseñas no coinciden.");
            }

            $data = $_POST;
            $resultado = $this->usuario->registrar($data);

            if ($resultado) {
                require_once __DIR__ . '/../views/registro/RegistroExitoso.php';
                exit;
            } else {
                throw new Exception("No se pudo registrar el usuario.");
            }
        } catch (Exception $e) {
            // Redirigir de vuelta al formulario con el error
            header("Location: " . URLROOT . "/Signup/mostrarFormulario?error=" . urlencode($e->getMessage()));
            exit;
        }
    }
}
?>