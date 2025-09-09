<?php
// controllers/InvitationController.php
class InvitationController extends BaseController {
    
    private $invitationModel;
    
    public function __construct() {
        $this->invitationModel = new Invitation();
    }
    
    public function index() {
        // Verificar autenticación
        if (!SessionManager::isLoggedIn()) {
            $this->redirect('/auth/login');
            return;
        }
        
        $userId = SessionManager::getUserId();
        $userName = SessionManager::getUserName();
        
        // Obtener invitaciones y estadísticas
        $invitations = $this->invitationModel->getByUserId($userId);
        $stats = $this->invitationModel->getStatsByUserId($userId);
        
        // Procesar mensaje de éxito/error
        $message = $_GET['msg'] ?? null;
        $error = $_GET['error'] ?? null;
        
        $this->render('invitations/index', [
            'invitations' => $invitations,
            'stats' => $stats,
            'userName' => $userName,
            'message' => $message,
            'error' => $error
        ]);
    }
    
    public function accept() {
        if (!SessionManager::isLoggedIn()) {
            $this->redirect('/auth/login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/invitations');
            return;
        }
        
        $invitationId = $_POST['id_invitacion'] ?? null;
        $userId = SessionManager::getUserId();
        
        if (!$invitationId) {
            $this->redirect('/invitations?error=' . urlencode('ID de invitación no válido'));
            return;
        }
        
        if ($this->invitationModel->accept($invitationId, $userId)) {
            $message = "Invitación aceptada correctamente. Se ha creado tu solicitud de empleo.";
            $this->redirect('/invitations?msg=' . urlencode($message));
        } else {
            $error = "Error al procesar la invitación.";
            $this->redirect('/invitations?error=' . urlencode($error));
        }
    }
    
    public function reject() {
        if (!SessionManager::isLoggedIn()) {
            $this->redirect('/auth/login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/invitations');
            return;
        }
        
        $invitationId = $_POST['id_invitacion'] ?? null;
        $userId = SessionManager::getUserId();
        
        if (!$invitationId) {
            $this->redirect('/invitations?error=' . urlencode('ID de invitación no válido'));
            return;
        }
        
        if ($this->invitationModel->reject($invitationId, $userId)) {
            $message = "Invitación rechazada.";
            $this->redirect('/invitations?msg=' . urlencode($message));
        } else {
            $error = "Error al procesar la invitación.";
            $this->redirect('/invitations?error=' . urlencode($error));
        }
    }
    
    // Método para AJAX - obtener invitaciones
    public function api() {
        if (!SessionManager::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        
        header('Content-Type: application/json');
        
        $userId = SessionManager::getUserId();
        $invitations = $this->invitationModel->getByUserId($userId);
        $stats = $this->invitationModel->getStatsByUserId($userId);
        
        echo json_encode([
            'invitations' => $invitations,
            'stats' => $stats
        ]);
        exit;
    }
    
    // Método para procesar acciones via AJAX
    public function process() {
        if (!SessionManager::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        header('Content-Type: application/json');
        
        $invitationId = $_POST['id_invitacion'] ?? null;
        $action = $_POST['accion'] ?? null;
        $userId = SessionManager::getUserId();
        
        if (!$invitationId || !$action) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }
        
        $success = false;
        $message = '';
        
        switch ($action) {
            case 'aceptar':
                $success = $this->invitationModel->accept($invitationId, $userId);
                $message = $success ? 
                    'Invitación aceptada correctamente. Se ha creado tu solicitud de empleo.' : 
                    'Error al aceptar la invitación.';
                break;
                
            case 'rechazar':
                $success = $this->invitationModel->reject($invitationId, $userId);
                $message = $success ? 
                    'Invitación rechazada.' : 
                    'Error al rechazar la invitación.';
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
                exit;
        }
        
        echo json_encode([
            'success' => $success,
            'message' => $message
        ]);
        exit;
    }
}
?>