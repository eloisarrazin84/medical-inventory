<?php
include '../includes/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permet l'accès depuis JS

try {
    $stmt = $pdo->query("SELECT id, nom, latitude, longitude, statut FROM evenements WHERE statut = 'En cours'");
    $evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($evenements, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur SQL: " . $e->getMessage()]);
}
?>
