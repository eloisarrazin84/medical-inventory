<?php
include '../includes/db.php';

$pdo->query("UPDATE notifications SET is_read = 1");

echo json_encode(["success" => true]);
?>
