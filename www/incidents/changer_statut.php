<?php
include '../includes/db.php';
include '../includes/auth.php';

$id = $_GET['id'];
$statut = $_GET['statut'];

$stmt = $pdo->prepare("UPDATE incidents SET statut = ? WHERE id = ?");
$stmt->execute([$statut, $id]);

header("Location: incidents.php");
exit;
?>
