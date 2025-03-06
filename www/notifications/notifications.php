<?php
header('Content-Type: application/json'); // Déclare que la réponse est en JSON
include '../includes/db.php';

// Requête pour récupérer les notifications non lues
$stmt = $pdo->prepare("SELECT id, message, type FROM notifications WHERE status = 0 ORDER BY created_at DESC");
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nettoie tout affichage parasite
ob_clean();

// Affiche le JSON proprement
echo json_encode($notifications);
?>
