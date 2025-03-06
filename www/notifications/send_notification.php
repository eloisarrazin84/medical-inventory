<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];
    $type = $_POST['type']; // info, warning, danger

    $stmt = $pdo->prepare("INSERT INTO notifications (message, type) VALUES (?, ?)");
    $stmt->execute([$message, $type]);

    echo json_encode(["success" => true]);
}
?>
