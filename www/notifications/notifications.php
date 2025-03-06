<?php
include '../includes/db.php'; // Vérifie que ce chemin est correct

header('Content-Type: application/json');

// Requête pour récupérer les notifications non lues
$stmt = $pdo->prepare("SELECT id, message, type FROM notifications WHERE status = 0 ORDER BY created_at DESC");
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($notifications);
?>

