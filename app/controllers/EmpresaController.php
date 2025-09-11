<?php
require_once __DIR__ . '/../../libraries/Database.php';
require_once __DIR__ . '/../models/Crear_oferta.php';
require_once __DIR__ . '/../models/CandidatoModel.php';
require_once __DIR__ . '/../models/DashboardEmpresa.php';
use App\Models\DashboardEmpresa;
class EmpresaController {
    private $dashboardModel;
    private $pdo;
    private $ofertaModel;
    private $candidatoModel;

    public function __construct() {
        $pdo = (new Database())->getConnection();
        $this->pdo = $pdo;
        $this->ofertaModel = new OfertaModel($pdo);
        $this->candidatoModel = new CandidatoModel($pdo);
        $this->pdo = (new Database())->getConnection(); 
        $this->dashboardModel = new DashboardEmpresa($this->pdo);

        // Verificar que sea empresa
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
            header("Location: " . URLROOT . "/Login/index");
            exit;
        }
    }

    public function dashboard() {
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

        require_once __DIR__ . '/../views/empresa/dashboard.php';
    }

    public function crearOferta() {
        $mensaje = "";
        $tipo_mensaje = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'titulo' => $_POST['titulo'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'ubicacion' => $_POST['ubicacion'] ?? '',
                'tipo_contrato' => $_POST['tipo_contrato'] ?? '',
                'id_usuario' => $_SESSION['usuario_id']
            ];

            if ($data['titulo'] && $data['descripcion']) {
                if ($this->ofertaModel->crear($data)) {
                    $mensaje = "Oferta publicada exitosamente.";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "Error al publicar la oferta.";
                    $tipo_mensaje = "error";
                }
            } else {
                $mensaje = "Todos los campos obligatorios deben estar completos.";
                $tipo_mensaje = "error";
            }
        }

        require_once __DIR__ . '/../views/empresa/crear_oferta.php';
    }

    public function verOfertas() {
        $empresaId = $_SESSION['usuario_id'];
        $mensaje = "";

        // Procesar acciones (eliminar/cerrar)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idOferta = $_POST['id_oferta'] ?? null;
            $accion = $_POST['accion'] ?? null;

            if ($idOferta && $accion) {
                if ($this->ofertaModel->esPropiedad($idOferta, $empresaId)) {
                    if ($accion === 'eliminar') {
                        if ($this->ofertaModel->eliminar($idOferta)) {
                            $mensaje = "Oferta eliminada correctamente.";
                        }
                    } elseif ($accion === 'cerrar') {
                        if ($this->ofertaModel->cambiarEstado($idOferta, 'Inactiva')) {
                            $mensaje = "Oferta cerrada correctamente.";
                        }
                    }
                }
            }
        }

        $ofertas = $this->ofertaModel->getOfertasEmpresa($empresaId);
        require_once __DIR__ . '/../views/empresa/ver_ofertas.php';
    }

    public function invitarCandidatos() {
        $empresaId = $_SESSION['usuario_id'];
        $success = null;
        $error = null;

        // Procesar envío de invitación
        if ($_POST && isset($_POST['enviar_invitacion'])) {
            $candidatoId = $_POST['candidato_id'];
            $puestoId = $_POST['puesto_id'];
            $mensaje = $_POST['mensaje'] ?? '';

            $resultado = $this->candidatoModel->enviarInvitacion($empresaId, $candidatoId, $puestoId, $mensaje);
            
            if ($resultado['success']) {
                $success = $resultado['message'];
            } else {
                $error = $resultado['message'];
            }
        }

        $candidatos = $this->candidatoModel->getCandidatosConPerfil();
        $ofertasActivas = $this->ofertaModel->getOfertasActivas($empresaId);

        require_once __DIR__ . '/../views/empresa/invitar_candidatos.php';
    }

    public function logout() {
        session_destroy();
        header("Location: " . URLROOT . "/Login/index");
        exit;
    }
}
?>