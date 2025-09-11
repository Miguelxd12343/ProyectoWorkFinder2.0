<?php
require_once __DIR__ . '/../../libraries/Database.php';
require_once __DIR__ . '/../../libraries/MailService.php';   // <-- ESTA LÍNEA
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
        // Validaciones básicas
        if (empty($_POST['nombre_empresa']) || empty($_POST['email']) || 
            empty($_POST['contrasena']) || empty($_POST['direccion']) ||
            empty($_POST['nit_cif']) || empty($_POST['telefono'])) {
            throw new Exception("Todos los campos son obligatorios.");
        }

        if ($_POST['contrasena'] !== $_POST['confirm_password']) {
            throw new Exception("Las contraseñas no coinciden.");
        }

        // Validación manual del email único
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuario WHERE Email = ?");
        $stmt->execute([trim(strtolower($_POST['email']))]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Este correo electrónico ya está registrado.");
        }

        // Iniciar transacción manual
        $this->pdo->beginTransaction();

        try {
            // 1. Insertar usuario
            $contrasenaHash = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
            
            $stmt = $this->pdo->prepare("INSERT INTO usuario (Nombre, Email, Contrasena, IdRol) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([
                trim($_POST['nombre_empresa']),
                trim(strtolower($_POST['email'])),
                $contrasenaHash,
                1
            ]);

            if (!$result) {
                throw new Exception("Error al crear usuario");
            }

            $idUsuario = $this->pdo->lastInsertId();

            // 2. Insertar empresa asociada (solo con las columnas que existen)
            $stmt = $this->pdo->prepare("INSERT INTO empresaasociada (NombreEmpresa, Direccion, Telefono) VALUES (?, ?, ?)");
            $result = $stmt->execute([
                trim($_POST['nombre_empresa']),
                trim($_POST['direccion']),
                trim($_POST['telefono'])
            ]);

            if (!$result) {
                throw new Exception("Error al registrar datos de empresa: " . implode(", ", $stmt->errorInfo()));
            }


            $this->pdo->commit();

            $mailService = new MailService();
            $mailService->enviarBienvenida($_POST['email'], $_POST['nombre_empresa'], 1);

            require_once __DIR__ . '/../views/registro/RegistroExitoso.php';
            exit;

        } catch (Exception $e) {

            $this->pdo->rollBack();
            throw $e;
        }

    } catch (Exception $e) {
        header("Location: " . URLROOT . "/SignupEmpresa/mostrarFormulario?error=" . urlencode($e->getMessage()));
        exit;
    }
    }
}
?>