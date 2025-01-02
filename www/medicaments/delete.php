<?php
include '../includes/db.php';

$id = $_GET['id'];

// Récupérer l'ID du sac avant suppression
$stmt = $pdo->prepare("SELECT sac_id FROM medicaments WHERE id = ?");
$stmt->execute([$id]);
$sac_id = $stmt->fetchColumn();

// Supprimer le médicament
$stmt = $pdo->prepare("DELETE FROM medicaments WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?sac_id=$sac_id");
exit;
