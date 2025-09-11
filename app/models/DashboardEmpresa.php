<?php
namespace App\Models;

use PDO;

class DashboardEmpresa {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getOfertasActivas($empresaId) {
        $stmt = $this->db->prepare("CALL sp_get_ofertas_activas_empresa(:empresaId)");
        $stmt->bindParam(':empresaId', $empresaId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchColumn() ?? 0;
        $stmt->closeCursor(); // ✅ importante
        return $result;
    }

    public function getTotalSolicitudes($empresaId) {
        $stmt = $this->db->prepare("CALL sp_get_total_solicitudes_empresa(:empresaId)");
        $stmt->bindParam(':empresaId', $empresaId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchColumn() ?? 0;
        $stmt->closeCursor();
        return $result;
    }

    public function getInvitacionesEnviadas($empresaId) {
        $stmt = $this->db->prepare("CALL sp_get_invitaciones_empresa(:empresaId)");
        $stmt->bindParam(':empresaId', $empresaId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchColumn() ?? 0;
        $stmt->closeCursor();
        return $result;
    }

    public function getCandidatosDisponibles() {
        $stmt = $this->db->query("CALL sp_get_candidatos_disponibles()");
        $result = $stmt->fetchColumn() ?? 0;
        $stmt->closeCursor();
        return $result;
    }
}
