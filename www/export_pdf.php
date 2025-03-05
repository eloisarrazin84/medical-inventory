<?php
require_once('tcpdf/tcpdf.php'); // Vérifie bien le chemin de TCPDF
include '../includes/db.php'; // Assurez-vous que la connexion fonctionne

// Vérification de la connexion
if (!$pdo) {
    die("Erreur de connexion à la base de données.");
}

// Récupération des médicaments expirés
$stmt = $pdo->query("
    SELECT sacs_medicaux.nom AS sac_nom, medicaments.nom AS med_nom, medicaments.date_expiration 
    FROM medicaments
    LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id
    WHERE medicaments.date_expiration < CURDATE()
    ORDER BY sacs_medicaux.nom
");
$expired_medicaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Regrouper les médicaments par sac
$grouped_medicaments = [];
$now = new DateTime();
foreach ($expired_medicaments as $med) {
    $exp_date = new DateTime($med['date_expiration']);
    $diff = $now->diff($exp_date)->days;
    
    // Définition de la gravité (🔴: critique, 🟠: modéré, 🟢: acceptable)
    $severity = ($diff > 60) ? '🔴' : (($diff > 30) ? '🟠' : '🟢');
    $med['severity'] = $severity;

    $grouped_medicaments[$med['sac_nom']][] = $med;
}

// Création du PDF
$pdf = new TCPDF();
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();

// Logo
$pdf->Image('logo.png', 10, 10, 30); // Vérifiez le chemin du logo
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, "Liste des Médicaments Expirés", 0, 1, 'C');
$pdf->Ln(5);

// Sommaire des sacs
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, "Sommaire des Sacs Médicaux", 0, 1);
$pdf->SetFont('helvetica', '', 11);
foreach ($grouped_medicaments as $sac_nom => $medicaments) {
    $pdf->Cell(0, 7, "• " . $sac_nom . " (" . count($medicaments) . " médicaments expirés)", 0, 1);
}
$pdf->Ln(5);

// Fonction pour récupérer une icône en fonction de la gravité
function getSeverityIcon($severity) {
    switch ($severity) {
        case '🔴': return 'icons/red.png';
        case '🟠': return 'icons/orange.png';
        case '🟢': return 'icons/green.png';
        default: return '';
    }
}

// Affichage des médicaments par sac
$pdf->SetFont('helvetica', 'B', 12);
foreach ($grouped_medicaments as $sac_nom => $medicaments) {
    $pdf->SetFillColor(220, 53, 69); // Rouge foncé pour l'en-tête
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 10, " $sac_nom ", 1, 1, 'C', 1);

    // En-tête du tableau
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Cell(100, 8, "💊 Médicament", 1, 0, 'C');
    $pdf->Cell(40, 8, "📅 Expiration", 1, 0, 'C');
    $pdf->Cell(30, 8, "⚠ Gravité", 1, 1, 'C');

    // Médicaments
    foreach ($medicaments as $med) {
        $iconPath = getSeverityIcon($med['severity']);

        $pdf->Cell(100, 8, '  ' . $med['med_nom'], 1);
        $pdf->Cell(40, 8, $med['date_expiration'], 1);
        
        if (!empty($iconPath)) {
            $pdf->Cell(30, 8, $pdf->Image($iconPath, $pdf->GetX() + 5, $pdf->GetY(), 6, 6), 1, 1, 'C');
        } else {
            $pdf->Cell(30, 8, "?", 1, 1, 'C');
        }
    }
    $pdf->Ln(5);
}

// Ajout d'un QR Code
$pdf->Ln(10);
$pdf->Cell(0, 8, "🔗 Accès rapide au tableau de bord :", 0, 1, 'C');
$pdf->Image('qrcode_dashboard.png', 90, $pdf->GetY(), 30, 30); // Générer un QR Code avec un outil comme https://www.qr-code-generator.com/

// Pied de page
$pdf->Ln(40);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 10, "Rapport généré par Outdoor Secours - " . date('d/m/Y'), 0, 1, 'C');

// Ajout d'un champ de signature
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, "Signature du Responsable :", 0, 1);
$pdf->Ln(15);
$pdf->Cell(80, 8, "________________________", 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(80, 8, "Nom & Prénom", 0, 1, 'L');

// Générer le PDF
$pdf->Output('medicaments_expires.pdf', 'D'); // 'D' pour forcer le téléchargement
