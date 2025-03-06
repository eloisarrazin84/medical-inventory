<?php
include '../includes/db.php'; // Vérifie que le chemin est bon

$stmt = $pdo->prepare("UPDATE notifications SET status = 1 WHERE status = 0");
$stmt->execute();

echo json_encode(["success" => true]);
?>

