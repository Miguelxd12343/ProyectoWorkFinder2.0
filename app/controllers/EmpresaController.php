<?php
require_once __DIR__ . '/../../libraries/Database.php';
require_once __DIR__ . '/../models/Crear_oferta.php';
require_once __DIR__ . '/../models/CandidatoModel.php';
require_once __DIR__ . '/../models/DashboardEmpresa.php';
require_once __DIR__ . '/../models/usuario.php';
use App\Models\DashboardEmpresa;
class EmpresaController {
    private $dashboardModel;
    private $pdo;
    private $ofertaModel;
    private $candidatoModel;
    private $usuarioModel;

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
    $formData = []; // Para mantener datos en caso de error

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'titulo' => trim($_POST['titulo'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'ubicacion' => trim($_POST['ubicacion'] ?? ''),
            'tipo_contrato' => $_POST['tipo_contrato'] ?? '',
            'id_usuario' => $_SESSION['usuario_id']
        ];

        $formData = $data; // Guardar para repoblar formulario

        // Validar datos
        $errores = $this->ofertaModel->validarOferta($data, $_SESSION['usuario_id']);

        if (empty($errores)) {
            if ($this->ofertaModel->crear($data)) {
                // Enviar notificación por correo (opcional)
                // $this->enviarNotificacionNuevaOferta($data);
                
                header("Location: " . URLROOT . "/Empresa/crearOferta?success=1");
                exit;
            } else {
                $mensaje = "Error al publicar la oferta.";
                $tipo_mensaje = "error";
            }
        } else {
            $mensaje = implode(' ', $errores);
            $tipo_mensaje = "error";
        }
    }

    // Verificar mensaje de éxito
    if (isset($_GET['success'])) {
        $mensaje = "Oferta publicada exitosamente.";
        $tipo_mensaje = "success";
    }

    require_once __DIR__ . '/../views/empresa/crear_oferta.php';
}

public function editarOferta() {
    $idOferta = $_GET['id'] ?? null;
    $empresaId = $_SESSION['usuario_id'];
    
    if (!$idOferta) {
        header("Location: " . URLROOT . "/Empresa/verOfertas");
        exit;
    }

    $oferta = $this->ofertaModel->obtenerOferta($idOferta, $empresaId);
    
    if (!$oferta) {
        header("Location: " . URLROOT . "/Empresa/verOfertas?error=" . urlencode("Oferta no encontrada"));
        exit;
    }

    $mensaje = "";
    $tipo_mensaje = "";
    $formData = $oferta; // Datos existentes

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'titulo' => trim($_POST['titulo'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'ubicacion' => trim($_POST['ubicacion'] ?? ''),
            'tipo_contrato' => $_POST['tipo_contrato'] ?? ''
        ];

        $formData = array_merge($oferta, $data); // Combinar datos

        // Validar datos
        $errores = $this->ofertaModel->validarOferta($data, $empresaId, $idOferta);

        if (empty($errores)) {
            if ($this->ofertaModel->actualizar($idOferta, $data)) {
                header("Location: " . URLROOT . "/Empresa/verOfertas?msg=" . urlencode("Oferta actualizada correctamente"));
                exit;
            } else {
                $mensaje = "Error al actualizar la oferta.";
                $tipo_mensaje = "error";
            }
        } else {
            $mensaje = implode(' ', $errores);
            $tipo_mensaje = "error";
        }
    }

        require_once __DIR__ . '/../views/empresa/editar_oferta.php';

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

    public function editarPerfil() {
    $empresaId = $_SESSION['usuario_id'];
    $mensaje = "";
    $tipo_mensaje = "";

    // Obtener datos actuales
    $usuarioActual = $this->usuarioModel->obtenerUsuarioPorId($empresaId);
    if (!$usuarioActual) {
        header("Location: " . URLROOT . "/Empresa/dashboard");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $datos = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'direccion_empresa' => trim($_POST['direccion_empresa'] ?? ''),
                'identificacion_fiscal' => trim($_POST['identificacion_fiscal'] ?? '')
            ];

            // Cambio de contraseña (opcional)
            if (!empty($_POST['nueva_contrasena'])) {
                if (empty($_POST['contrasena_actual'])) {
                    throw new Exception("Debes ingresar tu contraseña actual para cambiarla.");
                }
                $this->usuarioModel->cambiarContrasena($empresaId, $_POST['contrasena_actual'], $_POST['nueva_contrasena']);
            }

            $this->usuarioModel->actualizarPerfil($empresaId, $datos);
            
            // Actualizar sesión
            $_SESSION['usuario_nombre'] = $datos['nombre'];
            $_SESSION['usuario_email'] = $datos['email'];

            $mensaje = "Perfil actualizado correctamente.";
            $tipo_mensaje = "success";
            
            // Obtener datos actualizados
            $usuarioActual = $this->usuarioModel->obtenerUsuarioPorId($empresaId);

        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $tipo_mensaje = "error";
        }
    }

    require_once __DIR__ . '/../views/empresa/editar_perfil.php';
}

public function eliminarCuenta() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . URLROOT . "/Empresa/dashboard");
        exit;
    }

    $empresaId = $_SESSION['usuario_id'];
    
    try {
        if (!isset($_POST['confirm_delete']) || !isset($_POST['password_confirm'])) {
            throw new Exception("Faltan datos de confirmación.");
        }

        $this->usuarioModel->eliminarCuenta($empresaId, $_POST['password_confirm']);
        
        // Cerrar sesión
        session_destroy();
        
        header("Location: " . URLROOT . "/Login/index?msg=" . urlencode("Cuenta eliminada correctamente"));
        exit;

    } catch (Exception $e) {
        header("Location: " . URLROOT . "/Empresa/dashboard?error=" . urlencode($e->getMessage()));
        exit;
    }
}
}
?>