<?php
class CandidatoModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getCandidatosConPerfil() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.IdUsuario, u.Nombre, u.Email,
                       p.Edad, p.Telefono, p.Direccion, p.EmpleoDeseado, p.Descripcion, 
                       p.FotoPerfilPath, p.HojaDeVidaPath
                FROM usuario u
                LEFT JOIN perfilusuario p ON u.IdUsuario = p.IdUsuario
                WHERE u.IdRol = 2
                ORDER BY u.Nombre ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function enviarInvitacion($empresaId, $candidatoId, $puestoId, $mensaje) {
        try {
            // Verificar que no existe ya una invitación
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM invitaciones 
                WHERE IdEmpresa = ? AND IdCandidato = ? AND IdPuesto = ?
            ");
            $stmt->execute([$empresaId, $candidatoId, $puestoId]);

            if ($stmt->fetchColumn() > 0) {
                return [
                    'success' => false, 
                    'message' => "Ya has enviado una invitación a este candidato para este puesto."
                ];
            }

            // Crear la invitación
            $stmt = $this->pdo->prepare("
                INSERT INTO invitaciones (IdEmpresa, IdCandidato, IdPuesto, Mensaje) 
                VALUES (?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$empresaId, $candidatoId, $puestoId, $mensaje])) {
                return [
                    'success' => true, 
                    'message' => "Invitación enviada correctamente al candidato."
                ];
            }

            return [
                'success' => false, 
                'message' => "Error al enviar la invitación."
            ];
        } catch (PDOException $e) {
            return [
                'success' => false, 
                'message' => "Error al enviar la invitación: " . $e->getMessage()
            ];
        }
    }

    public function calcularEdad($fechaNacimiento) {
        if (!$fechaNacimiento) return 'No especificada';
        $fecha = new DateTime($fechaNacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha);
        return $edad->y . ' años';
    }
}
?>