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
    
    $userId = $_SESSION['usuario_id'];
    $userName = $_SESSION['usuario_nombre'];

    require_once __DIR__ . '/../models/PerfilModel.php';
    $perfilModel = new PerfilModel($this->pdo);

    $perfil = $perfilModel->obtenerPerfil($userId);
    $esNuevoPerfil = !$perfil;
    $mensaje = "";
    $tipo_mensaje = "";
    $errorCedula = "";
    $edadCalculada = "";
    
    // Obtener datos del usuario para validar cédula
    $usuarioData = $perfilModel->obtenerUsuario($userId);
    $cedulaOriginal = $usuarioData['IdentificacionFiscal'] ?? null;

    // Calcular edad si existe fecha de nacimiento
    if ($perfil && $perfil['Edad']) {
        $fechaNac = new DateTime($perfil['Edad']);
        $edadCalculada = $fechaNac->diff(new DateTime())->y . " años";
    }

    // Determinar qué campos bloquear
    $bloquearCamposBasicos = !$esNuevoPerfil;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'edad' => $_POST['edad'] ?? '',
            'cedula' => $_POST['cedula'] ?? '',
            'estado_civil' => $_POST['estado_civil'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'empleo_deseado' => $_POST['empleo_deseado'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
            'cvPath' => $perfil['HojaDeVidaPath'] ?? null,
            'fotoPath' => $perfil['FotoPerfilPath'] ?? null
        ];

        // Solo validar edad para nuevos perfiles
        if ($esNuevoPerfil && $data['edad']) {
            $fechaNac = new DateTime($data['edad']);
            $edad = $fechaNac->diff(new DateTime())->y;

            if ($edad < 18) {
                $mensaje = "Debes ser mayor de edad.";
                $tipo_mensaje = 'error';
            }
        }

        // Validar cédula solo para nuevos perfiles
        if ($esNuevoPerfil && $cedulaOriginal && $data['cedula'] !== $cedulaOriginal) {
            $errorCedula = "La cédula debe coincidir con la registrada en tu cuenta.";
        } elseif ($esNuevoPerfil && $perfilModel->existeCedula($data['cedula'], $userId)) {
            $errorCedula = "La cédula ya está registrada.";
        }

        // Solo procesar si no hay errores
        if (!$mensaje && !$errorCedula) {
            // Para perfiles existentes, usar la cédula y fecha existentes para las rutas de archivos
            $cedulaParaArchivos = $esNuevoPerfil ? $data['cedula'] : $perfil['Cedula'];
            
            // Subida de foto de perfil
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                $fileType = mime_content_type($_FILES['foto']['tmp_name']);
                
                if (in_array($fileType, $allowedTypes) && $_FILES['foto']['size'] <= 2 * 1024 * 1024) {
                    $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                    $nombreFoto = "perfil." . $extension;
                    $carpetaUsuario = __DIR__ . "/../../public/uploads/{$cedulaParaArchivos}/";
                    if (!is_dir($carpetaUsuario)) mkdir($carpetaUsuario, 0777, true);
                    $rutaFoto = $carpetaUsuario . $nombreFoto;
                    
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaFoto)) {
                        $data['fotoPath'] = "uploads/{$cedulaParaArchivos}/$nombreFoto";
                    }
                } else {
                    $mensaje = "La imagen debe ser JPG o PNG y menor de 2MB.";
                    $tipo_mensaje = 'error';
                }
            }

            // Subida de CV
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                if ($_FILES['cv']['size'] <= 5 * 1024 * 1024 && mime_content_type($_FILES['cv']['tmp_name']) === 'application/pdf') {
                    $nombreArchivo = "hojadevida.pdf";
                    $carpetaUsuario = __DIR__ . "/../../public/uploads/{$cedulaParaArchivos}/";
                    if (!is_dir($carpetaUsuario)) mkdir($carpetaUsuario, 0777, true);
                    $rutaCompleta = $carpetaUsuario . $nombreArchivo;
                    
                    if (move_uploaded_file($_FILES['cv']['tmp_name'], $rutaCompleta)) {
                        $data['cvPath'] = "uploads/{$cedulaParaArchivos}/$nombreArchivo";
                    }
                } else {
                    $mensaje = "El archivo debe ser PDF y menor de 5MB.";
                    $tipo_mensaje = 'error';
                }
            }

            // Guardar en DB solo si no hay errores
            if (!$mensaje) {
                if ($esNuevoPerfil) {
                    $perfilModel->crearPerfil($userId, $data);
                    $mensaje = "Perfil creado con éxito.";
                    $tipo_mensaje = 'success';
                } else {
                    $perfilModel->actualizarPerfil($userId, $data);
                    $mensaje = "Perfil actualizado.";
                    $tipo_mensaje = 'success';
                }

                // Actualizar datos
                $perfil = $perfilModel->obtenerPerfil($userId);
                $esNuevoPerfil = false;
                $bloquearCamposBasicos = true;
                
                if ($perfil && $perfil['Edad']) {
                    $fechaNac = new DateTime($perfil['Edad']);
                    $edadCalculada = $fechaNac->diff(new DateTime())->y . " años";
                }
            }
        }
    }

    $totalSolicitudes = $perfilModel->contarSolicitudes($userId);

    // Datos para la vista
    $data = [
        'nombreSesion' => $userName,
        'mensaje' => $mensaje,
        'tipo_mensaje' => $tipo_mensaje,
        'perfil' => $perfil,
        'esNuevoPerfil' => $esNuevoPerfil,
        'bloquearCamposBasicos' => $bloquearCamposBasicos,
        'errorCedula' => $errorCedula,
        'edadCalculada' => $edadCalculada,
        'totalSolicitudes' => $totalSolicitudes,
        'cedulaOriginal' => $cedulaOriginal
    ];

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
    public function enviarInvitacion($empresaId, $candidatoId, $puestoId, $mensaje) {
    try {
        // Verificar que no existe ya una invitación
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM invitaciones 
            WHERE IdEmpresa = ? AND IdCandidato = ? AND IdPuesto = ?
        ");
        $stmt->execute([$empresaId, $candidatoId, $puestoId]);

        if ($stmt->fetchColumn() > 0) {
            return [
                'success' => false, 
                'message' => "Ya has enviado una invitación a este candidato para este puesto."
            ];
        }

        // Obtener datos para el correo
        $datosInvitacion = $this->obtenerDatosInvitacion($empresaId, $candidatoId, $puestoId);

        // Crear la invitación
        $stmt = $this->pdo->prepare("
            INSERT INTO invitaciones (IdEmpresa, IdCandidato, IdPuesto, Mensaje) 
            VALUES (?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$empresaId, $candidatoId, $puestoId, $mensaje])) {
            // Enviar correo al candidato
            $this->enviarCorreoInvitacion($datosInvitacion, $mensaje);
            
            return [
                'success' => true, 
                'message' => "Invitación enviada correctamente al candidato."
            ];
        }

        return [
            'success' => false, 
            'message' => "Error al enviar la invitación."
        ];
    } catch (PDOException $e) {
        return [
            'success' => false, 
            'message' => "Error al enviar la invitación: " . $e->getMessage()
        ];
    }
}

private function obtenerDatosInvitacion($empresaId, $candidatoId, $puestoId) {
    $stmt = $this->pdo->prepare("
        SELECT 
            c.Nombre as nombreCandidato,
            c.Email as emailCandidato,
            e.Nombre as nombreEmpresa,
            p.Titulo as tituloOferta,
            p.Descripcion as descripcionOferta
        FROM usuario c
        JOIN usuario e ON e.IdUsuario = ?
        JOIN puestodetrabajo p ON p.IdPuesto = ?
        WHERE c.IdUsuario = ?
    ");
    $stmt->execute([$empresaId, $puestoId, $candidatoId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

private function enviarCorreoInvitacion($datos, $mensajePersonalizado) {
    require_once __DIR__ . '/../../libraries/MailService.php';
    $mailService = new MailService();
    
    $mailService->enviarInvitacionCandidato(
        $datos['emailCandidato'],
        $datos['nombreCandidato'],
        $datos['nombreEmpresa'],
        $datos['tituloOferta'],
        $mensajePersonalizado
    );
}

public function notificarPostulacion($empresaId, $candidatoId, $puestoId) {
    try {
        // Obtener datos para el correo
        $stmt = $this->pdo->prepare("
            SELECT 
                e.Email as emailEmpresa,
                e.Nombre as nombreEmpresa,
                c.Nombre as nombreCandidato,
                p.Titulo as tituloOferta
            FROM usuario e
            JOIN usuario c ON c.IdUsuario = ?
            JOIN puestodetrabajo p ON p.IdPuesto = ? AND p.IdUsuario = ?
            WHERE e.IdUsuario = ?
        ");
        $stmt->execute([$candidatoId, $puestoId, $empresaId, $empresaId]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($datos) {
            require_once __DIR__ . '/../../libraries/MailService.php';
            $mailService = new MailService();
            
            $mailService->enviarNotificacionPostulacion(
                $datos['emailEmpresa'],
                $datos['nombreEmpresa'],
                $datos['nombreCandidato'],
                $datos['tituloOferta']
            );
        }
    } catch (PDOException $e) {
        error_log("Error enviando notificación de postulación: " . $e->getMessage());
    }
}


}

