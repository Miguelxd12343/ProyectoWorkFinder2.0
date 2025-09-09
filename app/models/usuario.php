<?php
class Usuario {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Verificar si el email ya existe
    public function emailExiste(string $email): bool {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuario WHERE Email = ?");
            $stmt->execute([$email]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Validar datos antes del registro
    private function validarDatos(array $datos): array {
        $errores = [];

        // Validar nombre solo letras
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $datos['nombre'])) {
            $errores[] = "El nombre solo puede contener letras y espacios.";
        }

        // Validar email único
        if ($this->emailExiste($datos['email'])) {
            $errores[] = "Este correo electrónico ya está registrado.";
        }

        // Validar email formato
        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del correo electrónico no es válido.";
        }

        // Validar contraseña robusta
        $contrasena = $datos['contrasena'];
        if (strlen($contrasena) < 6) {
            $errores[] = "La contraseña debe tener al menos 6 caracteres.";
        }
        if (!preg_match('/[a-z]/', $contrasena)) {
            $errores[] = "La contraseña debe contener al menos una letra minúscula.";
        }
        if (!preg_match('/[A-Z]/', $contrasena)) {
            $errores[] = "La contraseña debe contener al menos una letra mayúscula.";
        }
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $contrasena)) {
            $errores[] = "La contraseña debe contener al menos un símbolo especial.";
        }

        return $errores;
    }

    public function registrar(array $datos): bool {
    try {
        // Validar datos
        $errores = $this->validarDatos($datos);
        
        if (!empty($errores)) {
            throw new Exception(implode(' ', $errores));
        }

        $contrasenaHash = password_hash($datos['contrasena'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario (Nombre, Email, Contrasena, IdRol, DireccionEmpresa, IdentificacionFiscal) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $ejecutado = $stmt->execute([
            trim($datos['nombre']),
            trim(strtolower($datos['email'])),
            $contrasenaHash,
            (int)$datos['rol'],
            $datos['direccion_empresa'] ?? null,
            $datos['identificacion_fiscal'] ?? null
        ]);

        return $ejecutado;
    } catch (Exception $e) {
        throw $e;
    }
}

// Agregar este método a tu clase Usuario existente

public function registrarEmpresa(array $datos): int|bool {
    try {
        // Validar datos
        $errores = $this->validarDatos($datos);
        
        if (!empty($errores)) {
            throw new Exception(implode(' ', $errores));
        }

        $contrasenaHash = password_hash($datos['contrasena'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario (Nombre, Email, Contrasena, IdRol) VALUES (?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $ejecutado = $stmt->execute([
            trim($datos['nombre']),
            trim(strtolower($datos['email'])),
            $contrasenaHash,
            (int)$datos['rol']
        ]);

        if ($ejecutado) {
            return $this->pdo->lastInsertId(); // Retornar el ID del usuario creado
        }
        
        return false;
    } catch (Exception $e) {
        throw $e;
    }
}

}
?>