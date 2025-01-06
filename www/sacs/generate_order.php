<?php
require '../vendor/autoload.php'; // Inclure l'autoload de Composer
include '../includes/db.php';

// Récupérer l'ID du sac
$sac_id = $_GET['sac_id'] ?? null;

if (!$sac_id) {
    die("ID du sac manquant.");
}

// Récupérer les informations du sac
$stmt = $pdo->prepare("
    SELECT sacs_medicaux.*, lieux_stockage.nom AS lieu_nom
    FROM sacs_medicaux
    LEFT JOIN lieux_stockage ON sacs_medicaux.lieu_id = lieux_stockage.id
    WHERE sacs_medicaux.id = ?
");
$stmt->execute([$sac_id]);
$sac = $stmt->fetch();

if (!$sac) {
    die("Sac introuvable.");
}

// Récupérer les médicaments expirés ou proches de l'expiration
$stmt = $pdo->prepare("
    SELECT medicaments.nom, medicaments.description, medicaments.quantite, medicaments.numero_lot, medicaments.date_expiration
    FROM medicaments
    WHERE sac_id = ? AND (date_expiration < CURDATE() OR date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY))
");
$stmt->execute([$sac_id]);
$medicaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($medicaments)) {
    die("Aucun médicament expiré ou proche de l'expiration pour ce sac.");
}

// Générer le PDF
use Dompdf\Dompdf;

$dompdf = new Dompdf();

$html = "
    <img src='https://outdoorsecours.fr/wp-content/uploads/2023/07/thumbnail_image001-1.png' alt='Logo Outdoor Secours' style='width: 150px; display: block; margin: 0 auto;'>
    <h1 style='text-align: center;'>Fiche de Commande</h1>
    <p><strong>Nom du Sac :</strong> {$sac['nom']}</p>
    <p><strong>Description :</strong> {$sac['description']}</p>
    <p><strong>Lieu de Stockage :</strong> {$sac['lieu_nom']}</p>
    <hr>
    <h2 style='text-align: center;'>Médicaments à Commander</h2>
    <table border='1' cellpadding='5' cellspacing='0' style='width: 100%; border-collapse: collapse;'>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Quantité</th>
                <th>Numéro de Lot</th>
                <th>Date d'Expiration</th>
            </tr>
        </thead>
        <tbody>";
foreach ($medicaments as $med) {
    $html .= "
        <tr>
            <td>{$med['nom']}</td>
            <td>{$med['description']}</td>
            <td>{$med['quantite']}</td>
            <td>{$med['numero_lot']}</td>
            <td>{$med['date_expiration']}</td>
        </tr>";
}
$html .= "
        </tbody>
    </table>
    <br><br>
    <p><strong>Visa :</strong> _______________________________</p>
";

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("fiche_commande_sac_{$sac['nom']}.pdf");
