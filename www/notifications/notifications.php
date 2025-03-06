<?php
include '../includes/db.php'; // Vérifie que la connexion PDO est bien incluse

try {
    // Récupérer les notifications non lues (status = 0) SANS les mettre à jour
    $stmt = $pdo->prepare("SELECT id, message, type FROM notifications WHERE status = 0 ORDER BY created_at DESC");
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retourner les notifications en JSON
    echo json_encode($notifications);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur SQL : " . $e->getMessage()]);
}
?>
