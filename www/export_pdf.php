<?php
require_once('vendor/autoload.php'); // Charge TCPDF via Composer

use TCPDF;

// Créer une instance de TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Outdoor Secours');
$pdf->SetTitle('Médicaments Expirés');
$pdf->SetSubject('Liste des Médicaments Expirés');
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Ajouter une page
$pdf->AddPage();

// Titre
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Liste des Médicaments Expirés', 0, 1, 'C');

// Connexion à la base de données
include '../includes/db.php';

// Récupérer les médicaments expirés
$stmt = $pdo->query("SELECT sacs_medicaux.nom AS sac_nom, medicaments.nom AS med_nom, medicaments.date_expiration FROM medicaments LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id WHERE medicaments.date_expiration < CURDATE() ORDER BY sacs_medicaux.nom");
$expired_medicaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Afficher la liste
$pdf->SetFont('helvetica', '', 12);
foreach ($expired_medicaments as $med) {
    $pdf->Cell(60, 10, $med['sac_nom'], 1);
    $pdf->Cell(70, 10, $med['med_nom'], 1);
    $pdf->Cell(40, 10, $med['date_expiration'], 1, 1);
}

// Télécharger le fichier PDF
$pdf->Output('medicaments_expires.pdf', 'D');
?>
