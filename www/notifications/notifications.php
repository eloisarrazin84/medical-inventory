<?php
include '../includes/db.php';

header('Content-Type: application/json');

// Récupérer les notifications non lues
$stmt = $pdo->query("SELECT id, message, type, link FROM notifications WHERE status = 0 ORDER BY created_at DESC");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($notifications);
?>
