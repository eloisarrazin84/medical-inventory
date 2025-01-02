<?php
include 'includes/db.php';

// Médicaments expirés
$stmt = $pdo->query("SELECT nom, date_expiration FROM medicaments WHERE date_expiration < CURDATE()");
$medicaments_expires = $stmt->fetchAll();

// Médicaments proches de l'expiration
$stmt = $pdo->query("SELECT nom, date_expiration FROM medicaments WHERE date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
$medicaments_proches_expiration = $stmt->fetchAll();

// Si des alertes existent, envoyer un e-mail
if (count($medicaments_expires) > 0 || count($medicaments_proches_expiration) > 0) {
    $to = 'votre-email@example.com'; // Remplacez par votre adresse e-mail
    $subject = 'Alertes Médicaments';
    $message = "Voici les alertes pour vos médicaments :\n\n";

    // Médicaments expirés
    if (count($medicaments_expires) > 0) {
        $message .= "Médicaments expirés :\n";
        foreach ($medicaments_expires as $med) {
            $message .= "- " . $med['nom'] . " (Expiré le " . $med['date_expiration'] . ")\n";
        }
        $message .= "\n";
    }

    // Médicaments proches de l'expiration
    if (count($medicaments_proches_expiration) > 0) {
        $message .= "Médicaments proches de l'expiration :\n";
        foreach ($medicaments_proches_expiration as $med) {
            $message .= "- " . $med['nom'] . " (Expire le " . $med['date_expiration'] . ")\n";
        }
    }

    // Envoyer l'e-mail
    mail($to, $subject, $message);
}
?>
