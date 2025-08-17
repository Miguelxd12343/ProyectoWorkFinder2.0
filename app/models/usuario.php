<?php
class Usuario {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function registrar(array $datos): void {
        $contrasenaHash = password_hash($datos['contrasena'], PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("CALL RegistrarUsuario(?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $datos['nombre'],
            $datos['email'],
            $contrasenaHash,
            $datos['rol'],
            $datos['direccion_empresa'] ?? null,
            $datos['identificacion_fiscal'] ?? null
        ]);
    }
}