<?php
include '../includes/db.php';
session_start();

// Vérifier que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Accès non autorisé.');
}

// Vérifier si un ID est passé en paramètre
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Erreur : ID utilisateur non spécifié.');
}

$id = $_GET['id'];

// Empêcher un administrateur de se supprimer lui-même
if ($_SESSION['user_id'] == $id) {
    die('Erreur : Vous ne pouvez pas vous supprimer vous-même.');
}

// Supprimer l'utilisateur de la base de données
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

// Rediriger vers la page de gestion des utilisateurs
header('Location: manage_users.php');
exit;
?>
