<?php
include '../includes/db.php';

// Vérifier les médicaments expirant dans moins de 30 jours
$stmt = $pdo->prepare("SELECT id, nom, date_expiration FROM medicaments WHERE date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
$stmt->execute();
$meds_expiring = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($meds_expiring as $med) {
    $message = "Le médicament {$med['nom']} expire bientôt ({$med['date_expiration']}) !";
    
    // Vérifier si une notification existe déjà pour ce médicament
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE message = ? AND status = 0");
    $check_stmt->execute([$message]);
    $already_notified = $check_stmt->fetchColumn();
    
    if ($already_notified == 0) {
        // Insérer une nouvelle notification
        $insert_stmt = $pdo->prepare("INSERT INTO notifications (message, type, status) VALUES (?, 'warning', 0)");
        $insert_stmt->execute([$message]);
    }
}
?>
