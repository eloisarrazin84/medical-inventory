<?php
include '../includes/db.php';
include '../includes/auth.php';

require '../vendor/autoload.php'; // Inclure l'autoload de Composer
use Endroid\QrCode\Builder\Builder;

$sac_id = $_GET['sac_id'] ?? null;

// Vérifier si le sac existe
$stmt = $pdo->prepare("SELECT * FROM sacs_medicaux WHERE id = ?");
$stmt->execute([$sac_id]);
$sac = $stmt->fetch();

if (!$sac) {
    die('Sac médical introuvable.');
}

// Générer le contenu du QR code
$url = "https://gestion.outdoorsecours.fr/sacs/inventaire.php?sac_id=" . $sac_id;

// Générer le QR code
$result = Builder::create()
    ->data($url)
    ->size(300)
    ->margin(10)
    ->build();

// Enregistrer le QR code dans un fichier
$filePath = "../qrcodes/sac_" . $sac_id . ".png";
file_put_contents($filePath, $result->getString());

// Afficher le QR code
header('Content-Type: image/png');
echo $result->getString();
