<?php
class EmpresaAsociada {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function crear(array $datos): bool {
        try {
            $sql = "INSERT INTO empresaasociada (IdUsuario, NombreEmpresa, Direccion, NIT_CIF, Telefono, Email) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $datos['id_usuario'],
                $datos['nombre_empresa'],
                $datos['direccion'],
                $datos['nit_cif'],
                $datos['telefono'],
                $datos['email']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>