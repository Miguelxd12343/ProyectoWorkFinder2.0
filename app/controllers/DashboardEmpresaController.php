<?php
use App\Models\DashboardEmpresa;
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../../libraries/Database.php';
require_once __DIR__ . '/../models/DashboardEmpresa.php';


class DashboardEmpresaController {
    private $dashboardModel;
    private $pdo;

    public function __construct() {
        $this->pdo = (new Database())->getConnection(); 
        $this->dashboardModel = new DashboardEmpresa($this->pdo);
    }

    public function index() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
            header("Location: " . URLROOT . "/Login/index");
            exit;
        }

        $empresaId = $_SESSION['usuario_id'];

        $data = [
            'nombreEmpresa'        => $_SESSION['usuario_nombre'] ?? 'Mi Empresa',
            'ofertasActivas'       => $this->dashboardModel->getOfertasActivas($empresaId),
            'totalSolicitudes'     => $this->dashboardModel->getTotalSolicitudes($empresaId),
            'invitacionesEnviadas' => $this->dashboardModel->getInvitacionesEnviadas($empresaId),
            'candidatosDisponibles'=> $this->dashboardModel->getCandidatosDisponibles()
        ];

        extract($data);
        
        require __DIR__ . '/../views/empresa/dashboard.php';

    }
}
