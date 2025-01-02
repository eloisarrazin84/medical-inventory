<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Inclure Composer et PHPMailer
require 'vendor/autoload.php';

function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        // Configurer le service SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.office365.com'; // Remplacez par votre hôte SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'no-reply@outdoorsecours.fr'; // Votre adresse e-mail
        $mail->Password   = 'Zot54389'; // Votre mot de passe ou clé d'application
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption TLS
        $mail->Port       = 587; // Port SMTP

        // Paramètres de l'expéditeur et du destinataire
        $mail->setFrom('no-reply@outdoorsecours.fr', 'Administrateur Outdoor Secours');
        $mail->addAddress($to); // Adresse e-mail du destinataire

        // Contenu de l'e-mail
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Message non envoyé. Erreur : {$mail->ErrorInfo}";
    }
}
?>
