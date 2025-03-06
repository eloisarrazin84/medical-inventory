<?php
include '../includes/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Récupérer les notifications non lues
$stmt = $pdo->prepare("SELECT id, message, type FROM notifications WHERE is_read = 0 ORDER BY created_at DESC");
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($notifications);
?>
