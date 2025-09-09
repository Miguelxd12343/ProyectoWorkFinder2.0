<?php
class SessionManager {
    private $timeout = 900;

    public function __construct($timeout = 900) {
        $this->timeout = $timeout;
        $this->checkSessionTimeout();
    }

    public function login($userId, $userName, $userRole) {
        $_SESSION['usuario_id'] = $userId;
        $_SESSION['usuario_nombre'] = $userName;
        $_SESSION['usuario_rol'] = $userRole;
        $_SESSION['last_activity'] = time();
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public function isLoggedIn() {
        return isset($_SESSION['usuario_id']);
    }

    public function checkSessionTimeout() {
        if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $this->timeout) {
            $this->logout();
            header("Location: " . URLROOT . "/Login/index?timeout=1");
            exit;
        } else {
            $_SESSION['last_activity'] = time();
        }
    }
}