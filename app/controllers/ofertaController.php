<?php
require_once __DIR__ . '/../models/OfertaModel.php';

class CandidatoController {
    private $ofertaModel;
    private $pdo;

    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->ofertaModel = new OfertaModel($pdo);
    }

    public function ofertas() {
        // Verifica login y rol de candidato
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 2) {
            header("Location: index.php?controller=Auth&action=login");
            exit;
        }

        $idUsuario = $_SESSION['usuario_id'];

        // ===== 1. Estadísticas rápidas =====
        $stats = [
            "total_ofertas" => $this->ofertaModel->contarOfertasActivas(),
            "total_empresas" => $this->ofertaModel->contarEmpresas(),
            "mis_postulaciones" => $this->ofertaModel->contarPostulaciones($idUsuario)
        ];

        // ===== 2. Filtros =====
        $filtros = [
            "ubicaciones" => $this->ofertaModel->obtenerUbicaciones(),
            "contratos" => $this->ofertaModel->obtenerContratos(),
            "seleccionada_ubicacion" => $_GET['ubicacion'] ?? '',
            "seleccionado_contrato" => $_GET['tipo_contrato'] ?? '',
            "busqueda" => $_GET['busqueda'] ?? ''
        ];

        // ===== 3. Ofertas filtradas =====
        $ofertas = $this->ofertaModel->obtenerOfertasFiltradas(
            $filtros["seleccionada_ubicacion"],
            $filtros["seleccionado_contrato"],
            $filtros["busqueda"],
            $idUsuario
        );

        // ===== 4. Cargar vista =====
        require __DIR__ . '/../views/candidato/ver_ofertas.php';
    }
}
