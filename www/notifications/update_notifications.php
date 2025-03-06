<?php
include '../includes/db.php';

$pdo->query("UPDATE notifications SET statuts = 1");

echo json_encode(["success" => true]);
?>
