<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'conexion.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    
    $stmt = $pdo->prepare("SELECT idUsuario FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", time() + 3600); //1 hora

        
        $stmt = $pdo->prepare("UPDATE usuario SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $stmt->execute([$token, $expiry, $email]);

        
        $link = "http://localhost/ProyectoWorkFinder2.0/PHP/reset-password.php?token=$token";

        
        $mensaje = "Hola,<br><br>" .
                   "Este es un correo para solicitar tu recuperación de contraseña.<br>" .
                   "Por favor, visita la siguiente página para restablecer tu contraseña:<br>" .
                   '<a href="' . $link . '">Restablecer Contraseña</a><br><br>' .
                   "Si no solicitaste esto, ignora este correo.";

        // Enviar correo
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sebastianmatth06@gmail.com';
            $mail->Password = 'lrxs cndd pwrw gqrz'; 
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('sebastianmatth06@gmail.com', 'WorkFinder');
            $mail->addAddress($email);
            $mail->Subject = 'Recuperar contraseña';
            $mail->Body = $mensaje;
            $mail->isHTML(true);

            $mail->send();
            header("Location: ../HTML/correo_enviado.html");
            exit();
        } catch (Exception $e) {
            echo "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    } else {
        echo "Correo no registrado.";
    }
}
?>