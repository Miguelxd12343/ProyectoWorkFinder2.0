<?php
require_once __DIR__ . '/../libraries/SessionManager.php';

class EmpresaController {
    private $session;

    public function __construct() {
        $this->session = new SessionManager();

        // ✅ Verificar si hay sesión y si el rol es 1 (Empresa)
        if (!$this->session->isLoggedIn() || $_SESSION['usuario_rol'] != 1) {
            header('Location: ' . URLROOT . '/Login/index?error=sin_permiso');
            exit;
        }
    }

    public function dashboard() {
        // Aquí cargas la vista del dashboard de Empresa
        require_once __DIR__ . '/../views/empresa/dashboard.php';
    }
}
