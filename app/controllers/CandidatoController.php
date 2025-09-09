<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../../libraries/Database.php';

class CandidatoController {
    private $usuarioModel;
    private $pdo;
    private $invitacionModel;

    public function __construct() {
        $this->pdo = (new Database())->getConnection();
        $this->usuarioModel = new Usuario($this->pdo);

       
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 2) {
            header("Location: " . URLROOT . "/Login/index");
            exit;
        }
    }

    public function dashboard() {
        $userId = $_SESSION['usuario_id'];
        $userName = $_SESSION['usuario_nombre'];

        try {
            // Ofertas activas
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM puestodetrabajo WHERE Estado = 'Activa'");
            $ofertasActivas = $stmt->fetchColumn();

            // Mis postulaciones
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM solicitudempleo WHERE IdUsuario = ?");
            $stmt->execute([$userId]);
            $misPostulaciones = $stmt->fetchColumn();

            // Postulaciones recientes
            $stmt = $this->pdo->prepare("
                SELECT s.*, p.Titulo, p.Empresa, s.FechaEnvio as FechaSolicitud
                FROM solicitudempleo s
                JOIN puestodetrabajo p ON s.IdPuestoTrabajo = p.IdPuesto
                WHERE s.IdUsuario = ?
                ORDER BY s.FechaEnvio DESC
                LIMIT 5
            ");
            $stmt->execute([$userId]);
            $postulacionesRecientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Invitaciones
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM invitaciones WHERE IdCandidato = ?");
            $stmt->execute([$userId]);
            $invitacionesPendientes = $stmt->fetchColumn();

        } catch (PDOException $e) {
            $ofertasActivas = 0;
            $misPostulaciones = 0;
            $postulacionesRecientes = [];
            $invitacionesPendientes = 0;
        }

        require_once __DIR__ . '/../views/candidato/dashboard.php';
    }


    public function perfil() {
    $this->verificarSesion();
    require_once __DIR__ . '/../views/candidato/perfil.php';
    $userId = $_SESSION['usuario_id'];
    $userName = $_SESSION['usuario_nombre'];

    require_once __DIR__ . '/../models/PerfilModel.php';
    $perfilModel = new PerfilModel($this->pdo);

    $perfil = $perfilModel->obtenerPerfil($userId);
    $esNuevoPerfil = !$perfil;
    $mensaje = "";
    $tipo_mensaje = "";
    $errorCedula = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'edad' => $_POST['edad'] ?? '',
            'cedula' => $_POST['cedula'] ?? '',
            'estado_civil' => $_POST['estado_civil'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'empleo_deseado' => $_POST['empleo_deseado'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
            'cvPath' => $perfil['HojaDeVidaPath'] ?? null
        ];

        // Validación edad >= 18
        $fechaNac = new DateTime($data['edad']);
        $edad = $fechaNac->diff(new DateTime())->y;

        if ($edad < 18) {
            $mensaje = "Debes ser mayor de edad.";
            $tipo_mensaje = 'error';
        } elseif ($perfilModel->existeCedula($data['cedula'], $userId)) {
            $errorCedula = "La cédula ya está registrada.";
        } else {
            // Subida de archivo CV
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                if ($_FILES['cv']['size'] <= 5 * 1024 * 1024 && mime_content_type($_FILES['cv']['tmp_name']) === 'application/pdf') {
                    $nombreArchivo = "hojadevida.pdf";
                    $carpetaUsuario = __DIR__ . "/../../public/uploads/{$data['cedula']}/";
                    if (!is_dir($carpetaUsuario)) mkdir($carpetaUsuario, 0777, true);
                    $rutaCompleta = $carpetaUsuario . $nombreArchivo;
                    move_uploaded_file($_FILES['cv']['tmp_name'], $rutaCompleta);
                    $data['cvPath'] = "uploads/{$data['cedula']}/$nombreArchivo";
                } else {
                    $mensaje = "El archivo debe ser PDF y menor de 5MB.";
                    $tipo_mensaje = 'error';
                }
            }

            // Guardar en DB
            if ($esNuevoPerfil) {
                $perfilModel->crearPerfil($userId, $data);
                $mensaje = "Perfil creado con éxito.";
                $tipo_mensaje = 'success';
            } else {
                $perfilModel->actualizarPerfil($userId, $data);
                $mensaje = "Perfil actualizado.";
                $tipo_mensaje = 'success';
            }

            $perfil = $perfilModel->obtenerPerfil($userId);
        }
    }

    $totalSolicitudes = $perfilModel->contarSolicitudes($userId);

    require_once __DIR__ . '/../views/candidato/perfil.php';
}
    public function ofertas() {
    $this->verificarSesion();
    require_once __DIR__ . '/../views/candidato/ver_ofertas.php';
     // Datos que quieras pasar a la vista, por ahora solo de la sesión
        $data = [
            'nombreSesion' => $_SESSION['usuario_nombre'] ?? '',
            'correoSesion' => $_SESSION['usuario_email'] ?? '',
            'rolSesion'    => $_SESSION['usuario_rol'] ?? '',
        ];

        // Renderizar la vista de perfil del candidato
        require_once __DIR__ . '/../views/candidato/ver_ofertas.php';
}

    public function postulaciones() {
    $this->verificarSesion();
    
    $userId = $_SESSION['usuario_id'];
    $userName = $_SESSION['usuario_nombre'];
    
    // Inicializar modelo
    require_once __DIR__ . '/../models/SolicitudModel.php';
    $solicitudModel = new SolicitudModel($this->pdo);
    
    // Procesar cancelación de solicitud
    if ($_POST && isset($_POST['action']) && $_POST['action'] === 'cancelar') {
        $solicitudId = $_POST['solicitud_id'] ?? null;
        
        if ($solicitudId) {
            $success = $solicitudModel->cancelarSolicitud($solicitudId, $userId);
            $mensaje = $success ? 
                "Solicitud cancelada correctamente." : 
                "No se pudo cancelar la solicitud. Solo se pueden cancelar solicitudes pendientes.";
            
            $param = $success ? 'msg' : 'error';
            header("Location: " . URLROOT . "/Candidato/postulaciones?{$param}=" . urlencode($mensaje));
            exit;
        }
    }
    
    // Obtener solicitudes y estadísticas
    $solicitudes = $solicitudModel->getByUserId($userId);
    $stats = $solicitudModel->getStatsByUserId($userId);
    
    // VERIFICAR QUE LAS VARIABLES EXISTAN
    if (!$solicitudes) {
        $solicitudes = [];
    }
    
    if (!$stats) {
        $stats = [
            'total' => 0,
            'pendientes' => 0,
            'aceptadas' => 0,
            'rechazadas' => 0
        ];
    }
    
    // Obtener mensaje de éxito/error
    $mensaje = $_GET['msg'] ?? null;
    $error = $_GET['error'] ?? null;
    
    require_once __DIR__ . '/../views/candidato/solicitudes.php';
}

    public function invitaciones() {
    $this->verificarSesion();
    
    $userId = $_SESSION['usuario_id'];
    $userName = $_SESSION['usuario_nombre'];
    
    if (!isset($this->invitacionModel)) {
        require_once __DIR__ . '/../models/invitacionesModel.php';
        $this->invitacionModel = new InvitacionModel($this->pdo);
    }

    // Procesar aceptar o rechazar invitación
    if ($_POST) {
        if (isset($_POST['accion']) && isset($_POST['id_invitacion'])) {
            $idInvitacion = $_POST['id_invitacion'];
            $accion = $_POST['accion'];
            
            $success = false;
            $mensaje = '';
            
            if ($accion === 'aceptar') {
                $success = $this->invitacionModel->accept($idInvitacion, $userId);
                $mensaje = $success ? 
                    "Invitación aceptada correctamente. Se ha creado tu solicitud de empleo." : 
                    "Error al procesar la invitación.";
            } elseif ($accion === 'rechazar') {
                $success = $this->invitacionModel->reject($idInvitacion, $userId);
                $mensaje = $success ? 
                    "Invitación rechazada." : 
                    "Error al procesar la invitación.";
            }
            
            // Redirigir con mensaje
            $param = $success ? 'msg' : 'error';
            header("Location: " . URLROOT . "/Candidato/invitaciones?{$param}=" . urlencode($mensaje));
            exit;
        }
    }

    // Obtener invitaciones y estadísticas
    $invitaciones = $this->invitacionModel->getByUserId($userId);
    $stats = $this->invitacionModel->getStatsByUserId($userId);
    
    if (!$stats) {
        $stats = [
            'total_invitations' => count($invitaciones),
            'opportunities' => count($invitaciones)
        ];
    }
    
    // Obtener mensaje de éxito/error
    $mensaje = $_GET['msg'] ?? null;
    $error = $_GET['error'] ?? null;

    require_once __DIR__ . '/../views/candidato/invitaciones.php';
    }


/**
 * Verifica sesión para evitar repetir código
 */
    private function verificarSesion() {
    if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 2) {
        header("Location: " . URLROOT . "/Login/index");
        exit;
    }
}

public function logout() {
    // Usar el SessionManager existente
    require_once __DIR__ . '/../../libraries/SessionManager.php';
    $sessionManager = new SessionManager();
    
    // Hacer logout
    $sessionManager->logout();
    
    // Redirigir al login
    header("Location: " . URLROOT . "/Login/index");
    exit;
}

}

