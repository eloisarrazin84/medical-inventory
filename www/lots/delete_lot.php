<?php
include '../includes/db.php';

// Vérifier si un ID est fourni
if (!isset($_GET['id'])) {
    die('Erreur : Aucun lot spécifié.');
}

$lot_id = (int)$_GET['id'];

// Supprimer le lot de la base de données
$stmt = $pdo->prepare("DELETE FROM lots WHERE id = ?");
if ($stmt->execute([$lot_id])) {
    header('Location: manage_lots.php?sac_id=' . $_GET['sac_id']); // Redirection après suppression
    exit;
} else {
    die('Erreur : Impossible de supprimer le lot.');
}
?>
