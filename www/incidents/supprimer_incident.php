<?php
include '../includes/db.php';

// Récupérer l'ID de l'incident
$incident_id = $_GET['id'] ?? null;

if (!$incident_id) {
    die('Erreur : Aucun incident spécifié.');
}

try {
    // Supprimer l'incident de la base de données
    $stmt = $pdo->prepare("DELETE FROM incidents WHERE id = ?");
    $stmt->execute([$incident_id]);

    // Redirection après suppression
   header("Location: /incidents/gestion_incidents.php?message=Incident supprimé avec succès");
    exit;
} catch (PDOException $e) {
    die('Erreur lors de la suppression : ' . $e->getMessage());
}
?>
