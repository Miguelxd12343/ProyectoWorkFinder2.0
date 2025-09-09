<?php
require_once __DIR__ . '/../libraries/SessionManager.php';

class AdminController {
    private $session;

    public function __construct() {
        $this->session = new SessionManager();

        // ✅ Verificar si hay sesión y si el rol es 3 (Admin)
        if (!$this->session->isLoggedIn() || $_SESSION['usuario_rol'] != 3) {
            header('Location: ' . URLROOT . '/Login/index?error=sin_permiso');
            exit;
        }
    }

    public function dashboard() {
        // Aquí cargas la vista del dashboard de Admin
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }
}
