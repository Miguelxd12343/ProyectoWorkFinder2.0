<?php
class OfertaModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Estadísticas
    public function contarOfertasActivas() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM puestodetrabajo WHERE Estado = 'Activa'");
        return $stmt->fetchColumn();
    }

    public function contarEmpresas() {
        $stmt = $this->pdo->query("SELECT COUNT(DISTINCT IdUsuario) FROM puestodetrabajo WHERE Estado = 'Activa'");
        return $stmt->fetchColumn();
    }

    public function contarPostulaciones($idUsuario) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM solicitudempleo WHERE IdUsuario = ?");
        $stmt->execute([$idUsuario]);
        return $stmt->fetchColumn();
    }

    // Filtros
    public function obtenerUbicaciones() {
        $stmt = $this->pdo->query("SELECT DISTINCT Ubicacion FROM puestodetrabajo WHERE Estado = 'Activa'");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function obtenerContratos() {
        $stmt = $this->pdo->query("SELECT DISTINCT TipoContrato FROM puestodetrabajo WHERE Estado = 'Activa'");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Ofertas con filtros y estado de postulación
    public function obtenerOfertasFiltradas($ubicacion, $contrato, $busqueda, $idUsuario) {
        $sql = "SELECT p.*, u.Nombre AS NombreEmpresa,
                       (SELECT COUNT(*) FROM solicitudempleo s 
                        WHERE s.IdUsuario = ? AND s.IdPuestoTrabajo = p.IdPuesto) as yaPostulado
                FROM puestodetrabajo p
                JOIN usuario u ON p.IdUsuario = u.IdUsuario
                WHERE p.Estado = 'Activa'";
        $params = [$idUsuario];

        if (!empty($ubicacion)) {
            $sql .= " AND p.Ubicacion = ?";
            $params[] = $ubicacion;
        }
        if (!empty($contrato)) {
            $sql .= " AND p.TipoContrato = ?";
            $params[] = $contrato;
        }
        if (!empty($busqueda)) {
            $sql .= " AND (p.Titulo LIKE ? OR p.Descripcion LIKE ?)";
            $params[] = "%$busqueda%";
            $params[] = "%$busqueda%";
        }

        $sql .= " ORDER BY p.FechaPublicacion DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
