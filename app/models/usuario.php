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

    public function crearTokenReset($email): array|bool {
    try {
        $stmt = $this->pdo->prepare("SELECT IdUsuario FROM usuario WHERE Email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            return false;
        }

        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", time() + 3600); // 1 hora

        $stmt = $this->pdo->prepare("UPDATE usuario SET reset_token = ?, token_expiry = ? WHERE Email = ?");
        $stmt->execute([$token, $expiry, $email]);

        return ['token' => $token, 'expiry' => $expiry];
    } catch (PDOException $e) {
        return false;
    }
}

    public function validarTokenReset($token): array|bool {
    try {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario WHERE reset_token = ? AND token_expiry > NOW()");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

    public function actualizarContrasenaConToken($token, $nuevaContrasena): bool {
    try {
        $usuario = $this->validarTokenReset($token);
        if (!$usuario) {
            return false;
        }

        $hashContrasena = password_hash($nuevaContrasena, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("
            UPDATE usuario 
            SET Contrasena = ?, reset_token = NULL, token_expiry = NULL 
            WHERE reset_token = ?
        ");
        
        return $stmt->execute([$hashContrasena, $token]);
    } catch (PDOException $e) {
        return false;
    }
}
// Agregar estos métodos al final de tu clase Usuario existente (antes del cierre de clase)

public function obtenerUsuarioPorId($id): array|bool {
    try {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario WHERE IdUsuario = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

public function actualizarPerfil($id, $datos): bool {
    try {
        // Validar que el email no esté en uso por otro usuario
        if (isset($datos['email'])) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuario WHERE Email = ? AND IdUsuario != ?");
            $stmt->execute([$datos['email'], $id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Este correo electrónico ya está en uso por otro usuario.");
            }
        }

        $campos = [];
        $valores = [];

        if (isset($datos['nombre']) && !empty($datos['nombre'])) {
            $campos[] = "Nombre = ?";
            $valores[] = trim($datos['nombre']);
        }

        if (isset($datos['email']) && !empty($datos['email'])) {
            if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("El formato del correo electrónico no es válido.");
            }
            $campos[] = "Email = ?";
            $valores[] = trim(strtolower($datos['email']));
        }

        if (isset($datos['direccion_empresa'])) {
            $campos[] = "DireccionEmpresa = ?";
            $valores[] = $datos['direccion_empresa'];
        }

        if (isset($datos['identificacion_fiscal'])) {
            $campos[] = "IdentificacionFiscal = ?";
            $valores[] = $datos['identificacion_fiscal'];
        }

        if (empty($campos)) {
            return true; // No hay nada que actualizar
        }

        $valores[] = $id; // Para el WHERE
        $sql = "UPDATE usuario SET " . implode(", ", $campos) . " WHERE IdUsuario = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($valores);

    } catch (Exception $e) {
        throw $e;
    }
}

public function cambiarContrasena($id, $contrasenaActual, $nuevaContrasena): bool {
    try {
        // Verificar contraseña actual
        $stmt = $this->pdo->prepare("SELECT Contrasena FROM usuario WHERE IdUsuario = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();

        if (!$usuario || !password_verify($contrasenaActual, $usuario['Contrasena'])) {
            throw new Exception("La contraseña actual es incorrecta.");
        }

        // Validar nueva contraseña
        if (strlen($nuevaContrasena) < 6) {
            throw new Exception("La nueva contraseña debe tener al menos 6 caracteres.");
        }

        $hashNueva = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare("UPDATE usuario SET Contrasena = ? WHERE IdUsuario = ?");
        return $stmt->execute([$hashNueva, $id]);

    } catch (Exception $e) {
        throw $e;
    }
}

public function verificarContrasena($id, $contrasena): bool {
    try {
        $stmt = $this->pdo->prepare("SELECT Contrasena FROM usuario WHERE IdUsuario = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();

        return $usuario && password_verify($contrasena, $usuario['Contrasena']);
    } catch (PDOException $e) {
        return false;
    }
}

public function eliminarCuenta($id, $contrasena): bool {
    try {
        // Verificar contraseña
        if (!$this->verificarContrasena($id, $contrasena)) {
            throw new Exception("Contraseña incorrecta.");
        }

        // Iniciar transacción
        $this->pdo->beginTransaction();

        // Obtener datos del usuario para limpieza
        $usuario = $this->obtenerUsuarioPorId($id);
        if (!$usuario) {
            throw new Exception("Usuario no encontrado.");
        }

        // Eliminar datos relacionados según el tipo de usuario
        if ($usuario['IdRol'] == 1) { // Empresa
            // Eliminar invitaciones enviadas
            $stmt = $this->pdo->prepare("DELETE FROM invitaciones WHERE IdEmpresa = ?");
            $stmt->execute([$id]);

            // Eliminar solicitudes recibidas en ofertas de esta empresa
            $stmt = $this->pdo->prepare("
                DELETE FROM solicitudempleo 
                WHERE IdPuestoTrabajo IN (
                    SELECT IdPuesto FROM puestodetrabajo WHERE IdUsuario = ?
                )
            ");
            $stmt->execute([$id]);

            // Eliminar ofertas de trabajo
            $stmt = $this->pdo->prepare("DELETE FROM puestodetrabajo WHERE IdUsuario = ?");
            $stmt->execute([$id]);

        } else { // Candidato
            // Eliminar solicitudes enviadas
            $stmt = $this->pdo->prepare("DELETE FROM solicitudempleo WHERE IdUsuario = ?");
            $stmt->execute([$id]);

            // Eliminar invitaciones recibidas
            $stmt = $this->pdo->prepare("DELETE FROM invitaciones WHERE IdCandidato = ?");
            $stmt->execute([$id]);

            // Eliminar perfil de usuario
            $stmt = $this->pdo->prepare("DELETE FROM perfilusuario WHERE IdUsuario = ?");
            $stmt->execute([$id]);

            // Eliminar archivos físicos si existen
            $this->eliminarArchivosUsuario($id);
        }

        // Finalmente eliminar el usuario
        $stmt = $this->pdo->prepare("DELETE FROM usuario WHERE IdUsuario = ?");
        $stmt->execute([$id]);

        $this->pdo->commit();
        return true;

    } catch (Exception $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}

private function eliminarArchivosUsuario($userId): void {
    try {
        // Obtener rutas de archivos del perfil
        $stmt = $this->pdo->prepare("SELECT FotoPerfilPath, HojaDeVidaPath FROM perfilusuario WHERE IdUsuario = ?");
        $stmt->execute([$userId]);
        $perfil = $stmt->fetch();

        if ($perfil) {
            $basePath = __DIR__ . '/../../public/';
            
            // Eliminar foto de perfil
            if ($perfil['FotoPerfilPath'] && file_exists($basePath . $perfil['FotoPerfilPath'])) {
                unlink($basePath . $perfil['FotoPerfilPath']);
            }

            // Eliminar CV
            if ($perfil['HojaDeVidaPath'] && file_exists($basePath . $perfil['HojaDeVidaPath'])) {
                unlink($basePath . $perfil['HojaDeVidaPath']);
            }

            // Intentar eliminar carpeta del usuario si está vacía
            if ($perfil['FotoPerfilPath']) {
                $userFolder = dirname($basePath . $perfil['FotoPerfilPath']);
                if (is_dir($userFolder) && count(scandir($userFolder)) == 2) { // Solo . y ..
                    rmdir($userFolder);
                }
            }
        }
    } catch (Exception $e) {
        // Log el error pero no detener el proceso
        error_log("Error eliminando archivos de usuario $userId: " . $e->getMessage());
    }
}
}
?>