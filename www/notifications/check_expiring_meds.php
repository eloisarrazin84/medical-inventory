<?php
include __DIR__ . '/../includes/db.php';

// Vérifier que la connexion à la base est bien établie
if (!isset($pdo)) {
    die("Erreur : connexion à la base de données non disponible !");
}

// Vérifier les médicaments expirant dans moins de 30 jours
$stmt = $pdo->prepare("
    SELECT id, nom, date_expiration 
    FROM medicaments 
    WHERE date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
");
$stmt->execute();
$meds_expiring = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($meds_expiring as $med) {
    $message = "Le médicament {$med['nom']} expire bientôt ({$med['date_expiration']}) !";

    // Vérifier si une notification existe déjà pour ce médicament
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE message LIKE ? AND status = 0");
    $check_stmt->execute(["%{$med['nom']}%"]);
    $already_notified = $check_stmt->fetchColumn();

    if ($already_notified == 0) {
        // Insérer une nouvelle notification
        $insert_stmt = $pdo->prepare("
            INSERT INTO notifications (message, type, status, created_at) 
            VALUES (?, 'warning', 0, NOW())
        ");
        $insert_stmt->execute([$message]);
    }
}

echo "Notifications mises à jour.";
?>
