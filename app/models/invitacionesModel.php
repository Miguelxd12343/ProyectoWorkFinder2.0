<?php
// models/InvitacionModel.php
class InvitacionModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getByUserId($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT i.*, p.Titulo, p.Descripcion, p.Ubicacion, p.TipoContrato, 
                       u.Nombre as NombreEmpresa, i.FechaInvitacion, i.Mensaje
                FROM invitaciones i
                JOIN puestodetrabajo p ON i.IdPuesto = p.IdPuesto
                JOIN usuario u ON i.IdEmpresa = u.IdUsuario
                WHERE i.IdCandidato = ?
                ORDER BY i.FechaInvitacion DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting invitations: " . $e->getMessage());
            return [];
        }
    }
    
    public function getInvitationDetails($invitationId, $candidateId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT i.*, p.Titulo, u.Nombre as NombreEmpresa 
                FROM invitaciones i
                JOIN puestodetrabajo p ON i.IdPuesto = p.IdPuesto
                JOIN usuario u ON i.IdEmpresa = u.IdUsuario
                WHERE i.IdInvitacion = ? AND i.IdCandidato = ?
            ");
            $stmt->execute([$invitationId, $candidateId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting invitation details: " . $e->getMessage());
            return false;
        }
    }
    
    public function accept($invitationId, $candidateId) {
        try {
            $this->pdo->beginTransaction();
            
            // Obtener datos de la invitación
            $invitation = $this->getInvitationDetails($invitationId, $candidateId);
            
            if (!$invitation) {
                throw new Exception("Invitación no encontrada");
            }
            
            // Crear solicitud de empleo
            $stmt = $this->pdo->prepare("
                INSERT INTO solicitudempleo (FechaEnvio, Estado, IdUsuario, IdPuestoTrabajo) 
                VALUES (CURDATE(), 'Enviada', ?, ?)
            ");
            $stmt->execute([$candidateId, $invitation['IdPuesto']]);
            
            // Eliminar la invitación
            $stmt = $this->pdo->prepare("DELETE FROM invitaciones WHERE IdInvitacion = ?");
            $stmt->execute([$invitationId]);
            
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error accepting invitation: " . $e->getMessage());
            return false;
        }
    }
    
    public function reject($invitationId, $candidateId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM invitaciones WHERE IdInvitacion = ? AND IdCandidato = ?");
            $result = $stmt->execute([$invitationId, $candidateId]);
            return $result;
        } catch (PDOException $e) {
            error_log("Error rejecting invitation: " . $e->getMessage());
            return false;
        }
    }
    
    public function getStatsByUserId($userId) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM invitaciones WHERE IdCandidato = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total_invitations' => $result['total'] ?? 0,
                'opportunities' => $result['total'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting invitation stats: " . $e->getMessage());
            return [
                'total_invitations' => 0,
                'opportunities' => 0
            ];
        }
    }
}
?>