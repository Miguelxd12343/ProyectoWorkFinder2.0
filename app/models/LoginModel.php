<?php
class LoginModel {
    private $db;

    public function __construct() {
    $this->db = (new Database())->getConnection(); // Instancia y obtiene conexión
}

    public function verificarUsuario($email) {
        $stmt = $this->db->prepare("CALL sp_LoginUsuario(?)");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}