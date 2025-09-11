<?php
class OfertaModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getEstadisticasEmpresa($empresaId) {
        try {
            $stats = [];

            // Ofertas activas
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM puestodetrabajo WHERE IdUsuario = ? AND Estado = 'Activa'");
            $stmt->execute([$empresaId]);
            $stats['ofertas_activas'] = $stmt->fetchColumn();

            // Total solicitudes recibidas
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM solicitudempleo s 
                JOIN puestodetrabajo p ON s.IdPuestoTrabajo = p.IdPuesto 
                WHERE p.IdUsuario = ?
            ");
            $stmt->execute([$empresaId]);
            $stats['total_solicitudes'] = $stmt->fetchColumn();

            // Invitaciones enviadas
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM invitaciones WHERE IdEmpresa = ?");
            $stmt->execute([$empresaId]);
            $stats['invitaciones_enviadas'] = $stmt->fetchColumn();

            // Candidatos disponibles
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuario WHERE IdRol = 2");
            $stmt->execute();
            $stats['candidatos_disponibles'] = $stmt->fetchColumn();

            return $stats;
        } catch (PDOException $e) {
            return [
                'ofertas_activas' => 0,
                'total_solicitudes' => 0,
                'invitaciones_enviadas' => 0,
                'candidatos_disponibles' => 0
            ];
        }
    }

    public function crear($data) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO puestodetrabajo (IdUsuario, Titulo, Descripcion, Ubicacion, TipoContrato, Estado) VALUES (?, ?, ?, ?, ?, 'Activa')");
            return $stmt->execute([
                $data['id_usuario'],
                $data['titulo'],
                $data['descripcion'],
                $data['ubicacion'],
                $data['tipo_contrato']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getOfertasEmpresa($empresaId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT pt.IdPuesto, pt.Titulo, pt.Descripcion, pt.Ubicacion, 
                       pt.TipoContrato, pt.FechaPublicacion, pt.Estado, u.Nombre AS Empresa
                FROM puestodetrabajo pt
                INNER JOIN usuario u ON pt.IdUsuario = u.IdUsuario
                WHERE pt.IdUsuario = ?
                ORDER BY pt.FechaPublicacion DESC
            ");
            $stmt->execute([$empresaId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function esPropiedad($idOferta, $empresaId) {
        try {
            $stmt = $this->pdo->prepare("SELECT IdUsuario FROM puestodetrabajo WHERE IdPuesto = ?");
            $stmt->execute([$idOferta]);
            $oferta = $stmt->fetch();
            return $oferta && $oferta['IdUsuario'] == $empresaId;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function eliminar($idOferta) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM puestodetrabajo WHERE IdPuesto = ?");
            return $stmt->execute([$idOferta]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function cambiarEstado($idOferta, $estado) {
        try {
            $stmt = $this->pdo->prepare("UPDATE puestodetrabajo SET Estado = ? WHERE IdPuesto = ?");
            return $stmt->execute([$estado, $idOferta]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getOfertasActivas($empresaId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT IdPuesto, Titulo FROM puestodetrabajo 
                WHERE IdUsuario = ? AND Estado = 'Activa' 
                ORDER BY Titulo ASC
            ");
            $stmt->execute([$empresaId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>