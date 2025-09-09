<?php
// models/SolicitudModel.php
class SolicitudModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getByUserId($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    s.IdSolicitud,
                    s.FechaEnvio,
                    s.Estado,
                    p.Titulo,
                    p.Descripcion,
                    p.Ubicacion,
                    p.TipoContrato,
                    u.Nombre as NombreEmpresa
                FROM solicitudempleo s
                INNER JOIN puestodetrabajo p ON s.IdPuestoTrabajo = p.IdPuesto
                INNER JOIN usuario u ON p.IdUsuario = u.IdUsuario
                WHERE s.IdUsuario = ?
                ORDER BY s.FechaEnvio DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function getStatsByUserId($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN Estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN Estado = 'Aceptada' THEN 1 ELSE 0 END) as aceptadas,
                    SUM(CASE WHEN Estado = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas
                FROM solicitudempleo 
                WHERE IdUsuario = ?
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total' => $result['total'] ?? 0,
                'pendientes' => $result['pendientes'] ?? 0,
                'aceptadas' => $result['aceptadas'] ?? 0,
                'rechazadas' => $result['rechazadas'] ?? 0
            ];
        } catch (PDOException $e) {
            return [
                'total' => 0,
                'pendientes' => 0,
                'aceptadas' => 0,
                'rechazadas' => 0
            ];
        }
    }
    
    public function cancelarSolicitud($solicitudId, $userId) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE solicitudempleo 
                SET Estado = 'Cancelada' 
                WHERE IdSolicitud = ? AND IdUsuario = ? AND Estado = 'Pendiente'
            ");
            $result = $stmt->execute([$solicitudId, $userId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>