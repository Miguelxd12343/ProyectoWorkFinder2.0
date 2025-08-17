<?php
class LoginController {
    private $loginModel;
    private $session;

    public function __construct() {
        $this->loginModel = new LoginModel();
        $this->session = new SessionManager();
    }

    public function index() {
        $error = $_GET['error'] ?? null;
        require_once 'app/views/login/index.php';
    }

    public function autenticar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['username'] ?? '';
            $pass = $_POST['password'] ?? '';

            $usuario = $this->loginModel->verificarUsuario($email);

            if ($usuario && password_verify($pass, $usuario['Contrasena'])) {
                $this->session->login($usuario['IdUsuario'], $usuario['Nombre'], $usuario['IdRol']);

                // Redirección según rol
                switch ($usuario['IdRol']) {
                    case 1:
                        header('Location: PHP/dashboard_empresa.php');
                        break;
                    case 2:
                        header('Location: PHP/dashboard_usuario.php');
                        break;
                    case 3:
                        header('Location: PHP/dashboard_administrador.php');
                        break;
                    default:
                        header('Location: login?error=rol');
                }
                exit;
            } else {
                header('Location: login?error=credenciales');
                exit;
            }
        }
    }
}