<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer-master/src/Exception.php';

class MailService {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configurarSMTP();
    }

    private function configurarSMTP() {
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'sebastianmatth06@gmail.com';
        $this->mail->Password = 'lrxs cndd pwrw gqrz'; 
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;

        // Configuración general
        $this->mail->CharSet = 'UTF-8';   
        $this->mail->Encoding = 'base64'; 
        $this->mail->setFrom('sebastianmatth06@gmail.com', 'WorkFinderPro');
        $this->mail->isHTML(true);
    }

    public function enviarRecuperacionContrasena($email, $token): bool {
        try {
            $link = URLROOT . "/PasswordReset/mostrarFormularioReset?token={$token}";
            
            $mensaje = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>WorkFinderPro</h1>
                    </div>
                    <div style='padding: 30px; background: white;'>
                        <h2 style='color: #333;'>Recuperación de Contraseña</h2>
                        <p>Hola,</p>
                        <p>Hemos recibido una solicitud para restablecer tu contraseña. Si fuiste tú, haz clic en el botón de abajo:</p>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$link}' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                                Restablecer Contraseña
                            </a>
                        </div>
                        <p>Este enlace expirará en 1 hora por seguridad.</p>
                        <p>Si no solicitaste esto, puedes ignorar este correo con seguridad.</p>
                        <p>Saludos,<br>El equipo de WorkFinderPro</p>
                    </div>
                    <div style='background: #f8f9fa; padding: 15px; text-align: center; color: #666; font-size: 12px;'>
                        © 2024 WorkFinderPro. Todos los derechos reservados.
                    </div>
                </div>
            ";

            $this->mail->clearAddresses();
            $this->mail->addAddress($email);
            $this->mail->Subject = 'Recuperación de Contraseña - WorkFinderPro';
            $this->mail->Body = $mensaje;

            return $this->mail->send();
        } catch (Exception $e) {
            return false;
        }
    }

    public function enviarBienvenida($email, $nombre, $tipo): bool {
        try {
            $tipoTexto = $tipo == 1 ? 'Empresa' : 'Candidato';
            
            
            $mensaje = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>¡Bienvenido a WorkFinderPro!</h1>
                    </div>
                    <div style='padding: 30px; background: white;'>
                        <h2 style='color: #333;'>¡Hola {$nombre}!</h2>
                        <p>¡Gracias por unirte a WorkFinderPro como {$tipoTexto}!</p>
                        <p>Tu cuenta ha sido creada exitosamente. Ya puedes comenzar a:</p>
                        <ul>
            ";
            
            if ($tipo == 1) {
                $mensaje .= "
                    <li>Publicar ofertas de empleo</li>
                    <li>Buscar candidatos talentosos</li>
                    <li>Gestionar tus procesos de selección</li>
                ";
            } else {
                $mensaje .= "
                    <li>Buscar ofertas de trabajo</li>
                    <li>Completar tu perfil profesional</li>
                    <li>Postularte a empleos</li>
                ";
            }
            
            $mensaje .= "
                        </ul>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='" . URLROOT . "/Login/index' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                                Iniciar Sesión
                            </a>
                        </div>
                        <p>¡Esperamos que tengas una gran experiencia en nuestra plataforma!</p>
                        <p>Saludos,<br>El equipo de WorkFinderPro</p>
                    </div>
                    <div style='background: #f8f9fa; padding: 15px; text-align: center; color: #666; font-size: 12px;'>
                        © 2024 WorkFinderPro. Todos los derechos reservados.
                    </div>
                </div>
            ";

            $this->mail->clearAddresses();
            $this->mail->addAddress($email);
            $this->mail->Subject = '¡Bienvenido a WorkFinderPro!';
            $this->mail->Body = $mensaje;

            return $this->mail->send();
        } catch (Exception $e) {
            return false;
        }
    }

    public function enviarInvitacionCandidato($emailCandidato, $nombreCandidato, $nombreEmpresa, $tituloOferta, $mensajePersonalizado) {
    try {
        $mensaje = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>¡Nueva Invitación Laboral!</h1>
                </div>
                <div style='padding: 30px; background: white;'>
                    <h2 style='color: #333;'>Hola {$nombreCandidato},</h2>
                    <p><strong>{$nombreEmpresa}</strong> está interesada en tu perfil y te ha enviado una invitación para el puesto:</p>
                    
                    <div style='background: #f8f9ff; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #667eea;'>
                        <h3 style='color: #667eea; margin: 0 0 10px 0;'>{$tituloOferta}</h3>
                    </div>
        ";

        if ($mensajePersonalizado) {
            $mensaje .= "
                <div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                    <strong>Mensaje de la empresa:</strong>
                    <p style='margin: 10px 0 0 0; font-style: italic;'>{$mensajePersonalizado}</p>
                </div>
            ";
        }

        $mensaje .= "
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . URLROOT . "/Login/index' style='background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                            Ver Invitación
                        </a>
                    </div>
                    
                    <p>Inicia sesión en WorkFinderPro para ver los detalles completos y responder a esta invitación.</p>
                    <p>¡Esta es una gran oportunidad para avanzar en tu carrera profesional!</p>
                    
                    <p>Saludos,<br>El equipo de WorkFinderPro</p>
                </div>
                <div style='background: #f8f9fa; padding: 15px; text-align: center; color: #666; font-size: 12px;'>
                    © 2024 WorkFinderPro. Todos los derechos reservados.
                </div>
            </div>
        ";

        $this->mail->clearAddresses();
        $this->mail->addAddress($emailCandidato);
        $this->mail->Subject = "Nueva invitación laboral de {$nombreEmpresa} - WorkFinderPro";
        $this->mail->Body = $mensaje;

        return $this->mail->send();
    } catch (Exception $e) {
        return false;
    }
}

public function enviarNotificacionPostulacion($emailEmpresa, $nombreEmpresa, $nombreCandidato, $tituloOferta) {
    try {
        $mensaje = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); padding: 20px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>¡Nueva Postulación Recibida!</h1>
                </div>
                <div style='padding: 30px; background: white;'>
                    <h2 style='color: #333;'>Hola {$nombreEmpresa},</h2>
                    <p>Tienes una nueva postulación para tu oferta de trabajo:</p>
                    
                    <div style='background: #dbeafe; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #1e40af;'>
                        <h3 style='color: #1e40af; margin: 0 0 10px 0;'>{$tituloOferta}</h3>
                        <p style='margin: 0; color: #1e293b;'><strong>Candidato:</strong> {$nombreCandidato}</p>
                    </div>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . URLROOT . "/Login/index' style='background: linear-gradient(135deg, #1e40af, #3b82f6); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                            Ver Postulación
                        </a>
                    </div>
                    
                    <p>Inicia sesión en tu panel de empresa para revisar el perfil completo del candidato y gestionar la postulación.</p>
                    
                    <p>Saludos,<br>El equipo de WorkFinderPro</p>
                </div>
                <div style='background: #f8f9fa; padding: 15px; text-align: center; color: #666; font-size: 12px;'>
                    © 2024 WorkFinderPro. Todos los derechos reservados.
                </div>
            </div>
        ";

        $this->mail->clearAddresses();
        $this->mail->addAddress($emailEmpresa);
        $this->mail->Subject = "Nueva postulación para {$tituloOferta} - WorkFinderPro";
        $this->mail->Body = $mensaje;

        return $this->mail->send();
        } catch (Exception $e) {
    // DEBUG: Ver el error exacto
    error_log("Error de correo: " . $e->getMessage());
    echo "Error específico: " . $e->getMessage(); // Solo para debug
    return false;
        }       
} 
  
}


