<?php
header('Content-Type: application/json'); // Déclare que la réponse est en JSON
include '../includes/db.php';

try {
    // Requête pour récupérer les notifications non lues
    $stmt = $pdo->prepare("SELECT id, message, type FROM notifications WHERE status = 0 ORDER BY created_at DESC");
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Nettoie tout affichage parasite avant de renvoyer le JSON
    if (ob_get_length()) ob_clean();
    
    // Affiche le JSON proprement
    echo json_encode($notifications);
} catch (Exception $e) {
    // En cas d'erreur, affiche un JSON d'erreur pour éviter le HTML
    echo json_encode(["error" => "Erreur SQL : " . $e->getMessage()]);
}
?>
