<?php
include __DIR__ . '/../includes/db.php';

// Vérifier que la connexion à la base est bien établie
if (!isset($pdo)) {
    die("Erreur : connexion à la base de données non disponible !");
}

// Vérifier les médicaments expirant dans moins de 30 jours
$stmt = $pdo->prepare("
    SELECT medicaments.id, medicaments.nom, medicaments.date_expiration, sacs_medicaux.id AS sac_id, sacs_medicaux.nom AS sac_nom 
    FROM medicaments 
    LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id 
    WHERE medicaments.date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
");
$stmt->execute();
$meds_expiring = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($meds_expiring as $med) {
    $message = "Le médicament <b>{$med['nom']}</b> dans le sac <b>{$med['sac_nom']}</b> expire bientôt ({$med['date_expiration']}) !";
    $link = "dashboard.php?sac_id={$med['sac_id']}"; // Redirection vers le bon sac

    // Vérifier si une notification existe déjà pour ce médicament
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE message LIKE ? AND status = 0");
    $check_stmt->execute(["%{$med['nom']}%"]);
    $already_notified = $check_stmt->fetchColumn();

    if ($already_notified == 0) {
        // Insérer une nouvelle notification avec le lien
        $insert_stmt = $pdo->prepare("
            INSERT INTO notifications (message, type, status, link, created_at) 
            VALUES (?, 'warning', 0, ?, NOW())
        ");
        $insert_stmt->execute([$message, $link]);
    }
}

echo "Notifications mises à jour.";
?>
