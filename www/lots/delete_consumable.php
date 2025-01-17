<?php
include '../includes/db.php';

// Vérifiez si l'ID du consommable est fourni
if (!isset($_GET['id'])) {
    die('Erreur : Aucun consommable spécifié.');
}

$cons_id = (int)$_GET['id'];

// Supprimez le consommable
$stmt = $pdo->prepare("DELETE FROM consommables WHERE id = ?");
if ($stmt->execute([$cons_id])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']); // Redirige vers la page précédente
    exit;
} else {
    die('Erreur : Impossible de supprimer le consommable.');
}
?>
