<?php
require_once __DIR__ . '/../../libraries/Database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../../libraries/MailService.php';

class PasswordResetController {
    private $usuario;
    private $mailService;

    public function __construct() {
        $pdo = (new Database())->getConnection();
        $this->usuario = new Usuario($pdo);
        $this->mailService = new MailService();
    }

    public function mostrarFormularioSolicitud() {
        $error = $_GET['error'] ?? null;
        require_once __DIR__ . '/../views/password/request_reset.php';
    }

    public function enviarEnlaceReset(): void {
        try {
            if (empty($_POST['email'])) {
                throw new Exception("El correo electrónico es obligatorio.");
            }

            $email = trim(strtolower($_POST['email']));
            $result = $this->usuario->crearTokenReset($email);

            if ($result) {
                $this->mailService->enviarRecuperacionContrasena($email, $result['token']);
                header("Location: " . URLROOT . "/PasswordReset/correoEnviado");
                exit;
            } else {
                throw new Exception("No se encontró una cuenta con ese correo electrónico.");
            }

        } catch (Exception $e) {
            header("Location: " . URLROOT . "/PasswordReset/mostrarFormularioSolicitud?error=" . urlencode($e->getMessage()));
            exit;
        }
    }

    public function mostrarFormularioReset() {
        $token = $_GET['token'] ?? null;
        $error = $_GET['error'] ?? null;

        if (!$token) {
            header("Location: " . URLROOT . "/Login/index?error=" . urlencode("Token inválido"));
            exit;
        }

        $usuario = $this->usuario->validarTokenReset($token);
        if (!$usuario) {
            header("Location: " . URLROOT . "/Login/index?error=" . urlencode("Token inválido o expirado"));
            exit;
        }

        require_once __DIR__ . '/../views/password/reset_form.php';
    }

    public function actualizarContrasena(): void {
        try {
            $token = $_POST['token'] ?? null;
            $password = $_POST['password'] ?? null;
            $confirmPassword = $_POST['confirm_password'] ?? null;

            if (!$token || !$password || !$confirmPassword) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            if ($password !== $confirmPassword) {
                throw new Exception("Las contraseñas no coinciden.");
            }

            // Validar contraseña robusta
            if (!$this->validarContrasenaRobusta($password)) {
                throw new Exception("La contraseña debe tener al menos 6 caracteres, una mayúscula, una minúscula y un símbolo especial.");
            }

            $resultado = $this->usuario->actualizarContrasenaConToken($token, $password);

            if ($resultado) {
                require_once __DIR__ . '/../views/password/reset_success.php';
                exit;
            } else {
                throw new Exception("Token inválido o expirado.");
            }

        } catch (Exception $e) {
            $token = $_POST['token'] ?? '';
            header("Location: " . URLROOT . "/PasswordReset/mostrarFormularioReset?token={$token}&error=" . urlencode($e->getMessage()));
            exit;
        }
    }

    public function correoEnviado() {
        require_once __DIR__ . '/../views/password/email_sent.php';
    }

    private function validarContrasenaRobusta($password): bool {
        return strlen($password) >= 6 &&
               preg_match('/[a-z]/', $password) &&
               preg_match('/[A-Z]/', $password) &&
               preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password);
    }
}
?>