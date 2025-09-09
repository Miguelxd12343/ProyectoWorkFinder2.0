<?php
require_once __DIR__ . '/../../libraries/Database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/EmpresaAsociada.php';

class SignupEmpresaController {
    private $usuario;
    private $empresaAsociada;
    private $pdo;

    public function __construct() {
        $this->pdo = (new Database())->getConnection();
        $this->usuario = new Usuario($this->pdo);
        $this->empresaAsociada = new EmpresaAsociada($this->pdo);
    }

    public function mostrarFormulario() {
        $error = $_GET['error'] ?? null;
        require_once __DIR__ . '/../views/registro/formEmpresa.php';
    }

    public function registrarEmpresa(): void {
        try {
            // Validar que vengan los datos necesarios
            if (empty($_POST['nombre_empresa']) || empty($_POST['email']) || 
                empty($_POST['contrasena']) || empty($_POST['direccion']) ||
                empty($_POST['nit_cif']) || empty($_POST['telefono'])) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            // Validar que las contraseñas coincidan
            if ($_POST['contrasena'] !== $_POST['confirm_password']) {
                throw new Exception("Las contraseñas no coinciden.");
            }

            // Iniciar transacción
            $this->pdo->beginTransaction();

            // Registrar usuario
            $datosUsuario = [
                'nombre' => $_POST['nombre_empresa'],
                'email' => $_POST['email'],
                'contrasena' => $_POST['contrasena'],
                'rol' => $_POST['rol']
            ];

            $idUsuario = $this->usuario->registrarEmpresa($datosUsuario);

            if (!$idUsuario) {
                throw new Exception("Error al registrar el usuario.");
            }

            // Registrar datos adicionales en empresaasociada
            $datosEmpresa = [
                'id_usuario' => $idUsuario,
                'nombre_empresa' => $_POST['nombre_empresa'],
                'direccion' => $_POST['direccion'],
                'nit_cif' => $_POST['nit_cif'],
                'telefono' => $_POST['telefono'],
                'email' => $_POST['email']
            ];

            $resultadoEmpresa = $this->empresaAsociada->crear($datosEmpresa);

            if (!$resultadoEmpresa) {
                throw new Exception("Error al registrar los datos de la empresa.");
            }

            // Confirmar transacción
            $this->pdo->commit();

            require_once __DIR__ . '/../views/registro/RegistroExitoso.php';
            exit;

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->pdo->rollBack();
            
            header("Location: " . URLROOT . "/SignupEmpresa/mostrarFormulario?error=" . urlencode($e->getMessage()));
            exit;
        }
    }
}
?>