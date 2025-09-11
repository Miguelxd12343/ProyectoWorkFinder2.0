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
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = 587;
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
            $dashboard = $tipo == 1 ? 'empresa' : 'candidato';
            
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
}
?>