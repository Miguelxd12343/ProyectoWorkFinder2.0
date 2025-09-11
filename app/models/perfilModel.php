<?php
class PerfilModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function obtenerPerfil($idUsuario) {
        $stmt = $this->db->prepare("SELECT * FROM perfilusuario WHERE IdUsuario = ?");
        $stmt->execute([$idUsuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerUsuario($idUsuario) {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE IdUsuario = ?");
        $stmt->execute([$idUsuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existeCedula($cedula, $idUsuario) {
        $stmt = $this->db->prepare("SELECT 1 FROM perfilusuario WHERE Cedula = ? AND IdUsuario != ?");
        $stmt->execute([$cedula, $idUsuario]);
        return $stmt->fetch();
    }

    public function crearPerfil($idUsuario, $data) {
        $sql = "INSERT INTO perfilusuario 
            (IdUsuario, Edad, Cedula, EstadoCivil, Telefono, Direccion, EmpleoDeseado, Descripcion, HojaDeVidaPath, FotoPerfilPath)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $idUsuario, $data['edad'], $data['cedula'], $data['estado_civil'],
            $data['telefono'], $data['direccion'], $data['empleo_deseado'],
            $data['descripcion'], $data['cvPath'], $data['fotoPath']
        ]);
    }

    public function actualizarPerfil($idUsuario, $data) {
        // Solo actualizar campos editables
        $sql = "UPDATE perfilusuario 
                SET Telefono=?, Direccion=?, EmpleoDeseado=?, Descripcion=?, HojaDeVidaPath=?, FotoPerfilPath=?
                WHERE IdUsuario=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['telefono'], $data['direccion'], $data['empleo_deseado'],
            $data['descripcion'], $data['cvPath'], $data['fotoPath'], $idUsuario
        ]);
    }

    public function contarSolicitudes($idUsuario) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM solicitudempleo WHERE IdUsuario = ?");
        $stmt->execute([$idUsuario]);
        return $stmt->fetchColumn();
    }
}
?>