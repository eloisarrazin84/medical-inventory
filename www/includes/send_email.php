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

        // Définir l'encodage UTF-8
        $mail->CharSet = 'UTF-8';

        // Paramètres de l'expéditeur et du destinataire
        $mail->setFrom('no-reply@outdoorsecours.fr', 'Administrateur Outdoor Secours');
        $mail->addAddress($to); // Adresse e-mail du destinataire

          // Contenu HTML avec le logo
        $header = "
            <div style='text-align: center;'>
                <img src='https://outdoorsecours.fr/wp-content/uploads/2023/07/thumbnail_image001-1.png' alt='Outdoor Secours' style='width: 200px; margin-bottom: 20px;'>
            </div>
        ";
        $footer = "
            <p style='text-align: center; margin-top: 20px; font-size: 12px; color: #666;'>
                Outdoor Secours © 2025 - Tous droits réservés.
            </p>
        ";
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $header . $content . $footer;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Message non envoyé. Erreur : {$mail->ErrorInfo}";
    }
}
?>
