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

    public function validarOferta($data, $empresaId, $ofertaId = null) {
        $errores = [];

        // Validar campos obligatorios
        if (empty(trim($data['titulo']))) {
            $errores[] = "El título es obligatorio.";
        }

        if (empty(trim($data['descripcion']))) {
            $errores[] = "La descripción es obligatoria.";
        }

        // Validar longitudes
        if (strlen($data['titulo']) > 100) {
            $errores[] = "El título no puede exceder 100 caracteres.";
        }

        if (strlen($data['descripcion']) < 25) {
            $errores[] = "La descripción debe tener al menos 25 caracteres.";
        }

        // Verificar duplicados
        if ($this->existeOfertaSimilar($data, $empresaId, $ofertaId)) {
            $errores[] = "Ya tienes una oferta muy similar publicada. Verifica el título y descripción.";
        }

        return $errores;
    }

    private function existeOfertaSimilar($data, $empresaId, $ofertaId = null) {
        try {
            $sql = "SELECT COUNT(*) FROM puestodetrabajo 
                    WHERE IdUsuario = ? AND (
                        LOWER(Titulo) = LOWER(?) OR
                        (LOWER(Titulo) LIKE LOWER(?) AND LOWER(Descripcion) LIKE LOWER(?))
                    )";
            
            $params = [
                $empresaId,
                trim($data['titulo']),
                '%' . trim($data['titulo']) . '%',
                '%' . substr(trim($data['descripcion']), 0, 100) . '%'
            ];

            // Si es edición, excluir la oferta actual
            if ($ofertaId) {
                $sql .= " AND IdPuesto != ?";
                $params[] = $ofertaId;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerOferta($idOferta, $empresaId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM puestodetrabajo 
                WHERE IdPuesto = ? AND IdUsuario = ?
            ");
            $stmt->execute([$idOferta, $empresaId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function actualizar($idOferta, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE puestodetrabajo 
                SET Titulo = ?, Descripcion = ?, Ubicacion = ?, TipoContrato = ?
                WHERE IdPuesto = ?
            ");
            return $stmt->execute([
                $data['titulo'],
                $data['descripcion'],
                $data['ubicacion'],
                $data['tipo_contrato'],
                $idOferta
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
}


?>