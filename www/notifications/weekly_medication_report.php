<?php
include '../includes/db.php';
include '../includes/send_email.php'; // Assurez-vous que ce fichier fonctionne correctement

// Récupérer les médicaments expirés
$stmt = $pdo->query("
    SELECT medicaments.nom AS med_nom, 
           medicaments.numero_lot, 
           medicaments.date_expiration, 
           sacs_medicaux.nom AS sac_nom, 
           lieux.nom AS lieu_nom
    FROM medicaments
    LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id
    LEFT JOIN lieux ON sacs_medicaux.lieu_id = lieux.id
    WHERE medicaments.date_expiration <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ORDER BY medicaments.date_expiration ASC
");
$medicaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier s'il y a des médicaments à signaler
if (empty($medicaments)) {
    die('Aucun médicament expiré ou proche de l\'expiration cette semaine.');
}

// Construire le contenu de l'email
$emailContent = "<h1>Récapitulatif des Médicaments Expirés ou Bientôt Expirés</h1>";
$emailContent .= "<table border='1' cellpadding='10' cellspacing='0'>";
$emailContent .= "<thead>
    <tr>
        <th>Nom du Médicament</th>
        <th>Numéro de Lot</th>
        <th>Date d'Expiration</th>
        <th>Nom du Sac</th>
        <th>Lieu</th>
    </tr>
</thead>";
$emailContent .= "<tbody>";

foreach ($medicaments as $med) {
    $emailContent .= "<tr>
        <td>" . htmlspecialchars($med['med_nom']) . "</td>
        <td>" . htmlspecialchars($med['numero_lot']) . "</td>
        <td>" . htmlspecialchars($med['date_expiration']) . "</td>
        <td>" . htmlspecialchars($med['sac_nom']) . "</td>
        <td>" . htmlspecialchars($med['lieu_nom']) . "</td>
    </tr>";
}

$emailContent .= "</tbody></table>";

// Paramètres de l'email
$to = "contact@outdoorsecours.fr"; // Adresse du destinataire
$subject = "Récapitulatif Hebdomadaire : Médicaments Expirés/Bientôt Expirés";
$headers = [
    'Content-Type: text/html; charset=UTF-8',
];

// Envoyer l'email
try {
    send_email($to, $subject, $emailContent);
    echo "Email envoyé avec succès.";
} catch (Exception $e) {
    die("Erreur lors de l'envoi de l'email : " . $e->getMessage());
}
?>
