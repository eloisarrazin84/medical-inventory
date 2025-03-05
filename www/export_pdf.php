<?php
require_once __DIR__ . '/vendor/autoload.php'; // Charger TCPDF via Composer

// Inclure la connexion à la base de données
include __DIR__ . '/includes/db.php';

// Vérifier la connexion PDO
if (!isset($pdo)) {
    die("Erreur de connexion à la base de données.");
}

// Créer une instance de TCPDF
class CustomPDF extends TCPDF {
    public function Footer() {
        $this->SetY(-20);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Rapport généré par Outdoor Secours - ' . date('d/m/Y'), 0, 0, 'C');
    }
}

$pdf = new CustomPDF();
$pdf->AddPage();

// ✅ **Ajouter un logo**
$logoPath = 'https://outdoorsecours.fr/wp-content/uploads/2023/07/thumbnail_image001-1.png';
$pdf->Image($logoPath, 10, 10, 30);
$pdf->SetY(20);

// ✅ **Ajouter un titre**
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Liste des Médicaments Expirés', 0, 1, 'C');
$pdf->Ln(10);

// ✅ **Récupérer les médicaments expirés**
$stmt = $pdo->query("SELECT sacs_medicaux.nom AS sac_nom, medicaments.nom AS med_nom, medicaments.date_expiration 
                     FROM medicaments 
                     LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id 
                     WHERE medicaments.date_expiration < CURDATE() 
                     ORDER BY sacs_medicaux.nom");
$expired_medicaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ **Regrouper par sac**
$grouped_medicaments = [];
$now = new DateTime();
foreach ($expired_medicaments as $med) {
    $exp_date = new DateTime($med['date_expiration']);
    $diff = $now->diff($exp_date)->days;
    $severity = ($diff > 60) ? '🔴' : (($diff > 30) ? '🟠' : '🟢');
    $med['severity'] = $severity;
    $grouped_medicaments[$med['sac_nom']][] = $med;
}

// ✅ **Créer un sommaire des sacs médicaux**
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Sommaire des Sacs Médicaux', 0, 1);
$pdf->SetFont('helvetica', '', 12);
foreach ($grouped_medicaments as $sac_nom => $medicaments) {
    $pdf->Cell(0, 10, '• ' . $sac_nom . ' (' . count($medicaments) . ' médicaments expirés)', 0, 1);
}
$pdf->Ln(10);

// ✅ **Créer un tableau avec des icônes**
foreach ($grouped_medicaments as $sac_nom => $medicaments) {
    // Titre du sac
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetFillColor(220, 50, 50);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 10, '📦 ' . $sac_nom, 1, 1, 'C', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 11);

    // En-tête du tableau
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(100, 8, '🏥 Médicament', 1, 0, 'C', true);
    $pdf->Cell(40, 8, '📆 Expiration', 1, 0, 'C', true);
    $pdf->Cell(30, 8, '⚠ Gravité', 1, 1, 'C', true);

    // Ajouter les médicaments
    foreach ($medicaments as $med) {
        $pdf->Cell(100, 8, '💊 ' . $med['med_nom'], 1);
        $pdf->Cell(40, 8, $med['date_expiration'], 1);
        $pdf->Cell(30, 8, $med['severity'], 1, 1, 'C');
    }

    $pdf->Ln(5);
}

// ✅ **Ajouter un QR Code pour accéder au tableau de bord**
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, '🔗 Accéder au Tableau de Bord', 0, 1, 'C');
$dashboard_url = "https://gestion.outdoorsecours.fr/dashboard.php";
$pdf->write2DBarcode($dashboard_url, 'QRCODE,H', 80, $pdf->GetY() + 5, 50, 50);
$pdf->Ln(55);

// ✅ **Ajouter un champ de signature**
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Signature du Responsable', 0, 1, 'L');
$pdf->Line(10, $pdf->GetY() + 5, 80, $pdf->GetY() + 5); // Ligne pour signature
$pdf->Ln(15);

// ✅ **Télécharger le fichier PDF**
$pdf->Output('medicaments_expires.pdf', 'D');
?>
