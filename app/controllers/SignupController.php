<?php
require_once 'app/models/Usuario.php';

class SignupController {
    private $usuarioModel;

    public function __construct($pdo) {
        $this->usuarioModel = new Usuario($pdo);
    }

    public function mostrarFormulario() {
        require 'app/views/registro/form.php';
    }

    public function procesarRegistro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->usuarioModel->registrar($_POST);
                header('Location: /registro/exito');
                exit;
            } catch (PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    echo "Error: El correo ya está registrado.";
                } else {
                    echo "Error en el registro: " . $e->getMessage();
                }
            }
        }
    }

    public function mostrarExito() {
        require 'app/views/registro/exito.php';
    }
}