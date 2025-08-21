<?php
require_once __DIR__ . '/../../libraries/Database.php';
require_once __DIR__ . '/../models/Usuario.php';

class SignupController {
    private $usuario;

    public function __construct() {
        $pdo = (new Database())->getConnection();
        $this->usuario = new Usuario($pdo);
    }

    // Muestra el formulario
    public function mostrarFormulario() {
        require_once __DIR__ . '/../views/registro/form.php';
    }

    // Procesa el registro
    public function registrarUsuario() {
    try {
        // Tomamos directamente lo que viene del formulario
        $data = $_POST;

        $resultado = $this->usuario->registrar($data);

        if ($resultado) {
            require_once __DIR__ . '/../views/registro/exitoso.php';
            exit;
        } else {
            echo "⚠️ No se pudo registrar el usuario.";
        }
    } catch (Exception $e) {
        echo "❌ Error en el registro: " . $e->getMessage();
    }
}

}
