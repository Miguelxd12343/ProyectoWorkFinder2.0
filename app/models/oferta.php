<?php
class OfertaModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Estadísticas
    public function contarOfertasActivas() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM ofertaempleo WHERE Estado = 'activa'");
        return $stmt->fetchColumn();
    }

    public function contarEmpresas() {
        $stmt = $this->pdo->query("SELECT COUNT(DISTINCT IdEmpresa) FROM ofertaempleo WHERE Estado = 'activa'");
        return $stmt->fetchColumn();
    }

    public function contarPostulaciones($idUsuario) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM solicitudempleo WHERE IdUsuario = ?");
        $stmt->execute([$idUsuario]);
        return $stmt->fetchColumn();
    }

    // Filtros
    public function obtenerUbicaciones() {
        $stmt = $this->pdo->query("SELECT DISTINCT Ubicacion FROM ofertaempleo WHERE Estado = 'activa'");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function obtenerContratos() {
        $stmt = $this->pdo->query("SELECT DISTINCT TipoContrato FROM ofertaempleo WHERE Estado = 'activa'");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Ofertas con filtros y estado de postulación
    public function obtenerOfertasFiltradas($ubicacion, $contrato, $busqueda, $idUsuario) {


        
        $sql = "SELECT o.*, e.NombreEmpresa,
                       (SELECT COUNT(*) FROM solicitudempleo s 
                        WHERE s.IdUsuario = ? AND s.IdPuesto = o.IdPuesto) as yaPostulado
                FROM ofertaempleo o
                JOIN empresa e ON o.IdEmpresa = e.IdEmpresa
                WHERE o.Estado = 'activa'";
        $params = [$idUsuario];

        if (!empty($ubicacion)) {
            $sql .= " AND o.Ubicacion = ?";
            $params[] = $ubicacion;
        }
        if (!empty($contrato)) {
            $sql .= " AND o.TipoContrato = ?";
            $params[] = $contrato;
        }
        if (!empty($busqueda)) {
            $sql .= " AND (o.Titulo LIKE ? OR o.Descripcion LIKE ?)";
            $params[] = "%$busqueda%";
            $params[] = "%$busqueda%";
        }

        $sql .= " ORDER BY o.FechaPublicacion DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
