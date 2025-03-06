<?php
include '../includes/db.php'; // Connexion à la BDD

try {
    // Met à jour les notifications pour les marquer comme lues
    $stmt = $pdo->prepare("UPDATE notifications SET status = 1 WHERE status = 0");
    $stmt->execute();

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur SQL : " . $e->getMessage()]);
}
?>
