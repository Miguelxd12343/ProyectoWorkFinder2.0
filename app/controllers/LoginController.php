<?php
class LoginController {
    private $loginModel;
    private $session;

    public function __construct() {
        $this->loginModel = new LoginModel();     // autoload models
        $this->session    = new SessionManager(); // autoload libraries
    }

    public function index() {
        $error = $_GET['error'] ?? null;
        require_once __DIR__ . '/../views/login/login.php';
    }

    public function autenticar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/Login/index');
            exit;
        }

        $email = $_POST['username'] ?? '';
        $pass  = $_POST['password'] ?? '';

        $usuario = $this->loginModel->verificarUsuario($email);

        if ($usuario && password_verify($pass, $usuario['Contrasena'])) {
            $this->session->login($usuario['IdUsuario'], $usuario['Nombre'], $usuario['IdRol']);

            switch ((int)$usuario['IdRol']) {
                case 1: header('Location: ' . URLROOT . '/Empresa/dashboard'); break;
                case 2: header('Location: ' . URLROOT . '/Usuario/dashboard'); break;
                case 3: header('Location: ' . URLROOT . '/Admin/dashboard');   break;
                default: header('Location: ' . URLROOT . '/Login/index?error=rol');
            }
            exit;
        }

        header('Location: ' . URLROOT . '/Login/index?error=credenciales');
        exit;
    }

    public function logout() {
        $this->session->logout();
        header('Location: ' . URLROOT . '/Home/index');
        exit;
    }
}
