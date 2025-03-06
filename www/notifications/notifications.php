<?php
include '../includes/db.php'; // Assure que la connexion PDO est bien incluse

try {
    // Préparer la requête SQL pour récupérer les notifications non lues (status = 0)
    $stmt = $pdo->prepare("SELECT message, type FROM notifications WHERE status = 0 ORDER BY created_at DESC");
    $stmt->execute();
    
    // Récupérer les résultats
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retourner les données en JSON
    echo json_encode($notifications);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur SQL : " . $e->getMessage()]);
}
?>
