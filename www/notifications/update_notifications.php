<?php
include '../includes/db.php';

$pdo->query("UPDATE notifications SET status = 1");

echo json_encode(["success" => true]);
?>
