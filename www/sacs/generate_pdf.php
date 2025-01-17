<?php
require '../vendor/autoload.php';
use Dompdf\Dompdf;

include '../includes/db.php';

// Vérifier si l'ID du sac est fourni
$sac_id = $_GET['sac_id'] ?? null;

if (!$sac_id) {
    die('Erreur : Aucun sac sélectionné.');
}

// Récupérer les informations du sac
$stmt = $pdo->prepare("SELECT * FROM sacs_medicaux WHERE id = ?");
$stmt->execute([$sac_id]);
$sac = $stmt->fetch();

if (!$sac) {
    die('Erreur : Sac médical introuvable.');
}

// Récupérer les médicaments associés au sac
$stmt = $pdo->prepare("SELECT * FROM medicaments WHERE sac_id = ?");
$stmt->execute([$sac_id]);
$medicaments = $stmt->fetchAll();

// Récupérer les lots associés au sac
$stmt = $pdo->prepare("SELECT * FROM lots WHERE sac_id = ?");
$stmt->execute([$sac_id]);
$lots = $stmt->fetchAll();

// Contenu HTML pour le PDF
$html = "
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Inventaire du Sac : {$sac['nom']}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
        h1 { color: #0056b3; text-align: center; }
        .logo { text-align: center; margin-bottom: 20px; }
        .logo img { height: 100px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .signature { margin-top: 50px; text-align: left; }
        .signature .box { border: 1px solid #000; width: 300px; height: 100px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class='logo'>
        <img src='https://outdoorsecours.fr/wp-content/uploads/2023/07/thumbnail_image001-1.png' alt='Logo Outdoor Secours'>
    </div>
    <h1>Inventaire du Sac : {$sac['nom']}</h1>
    <p><strong>Description :</strong> {$sac['description']}</p>
    <p><strong>Date de Création :</strong> {$sac['date_creation']}</p>
    
    <h2>Médicaments</h2>
    <table>
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
        
foreach ($medicaments as $medicament) {
    $html .= "
        <tr>
            <td>{$medicament['nom']}</td>
            <td>{$medicament['description']}</td>
            <td>{$medicament['quantite']}</td>
            <td>{$medicament['numero_lot']}</td>
            <td>{$medicament['date_expiration']}</td>
        </tr>";
}

$html .= "
        </tbody>
    </table>
    
    <h2>Lots et Consommables</h2>";
    
foreach ($lots as $lot) {
    $html .= "
    <h3>Lot : {$lot['nom']}</h3>
    <p><strong>Description :</strong> {$lot['description']}</p>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Quantité</th>
                <th>Date d'Expiration</th>
            </tr>
        </thead>
        <tbody>";
        
    // Récupérer les consommables associés au lot
    $stmt = $pdo->prepare("SELECT * FROM consommables WHERE lot_id = ?");
    $stmt->execute([$lot['id']]);
    $consommables = $stmt->fetchAll();
    
    if (!empty($consommables)) {
        foreach ($consommables as $consommable) {
            $html .= "
            <tr>
                <td>{$consommable['nom']}</td>
                <td>{$consommable['description']}</td>
                <td>{$consommable['quantite']}</td>
                <td>{$consommable['date_expiration']}</td>
            </tr>";
        }
    } else {
        $html .= "
            <tr>
                <td colspan='4'>Aucun consommable trouvé pour ce lot.</td>
            </tr>";
    }

    $html .= "
        </tbody>
    </table>";
}

$html .= "
    <div class='signature'>
        <p><strong>Visa du Responsable :</strong></p>
        <div class='box'></div>
    </div>
</body>
</html>";

// Créer le PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Inventaire_Sac_{$sac['nom']}.pdf", ["Attachment" => true]);
