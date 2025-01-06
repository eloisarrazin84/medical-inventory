<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Durée d'inactivité maximale en secondes (ex : 30 minutes)
define('INACTIVE_TIME', 1800); // 30 minutes

// Vérifier l'expiration de session
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > INACTIVE_TIME) {
    // Si inactif trop longtemps, détruire la session et rediriger
    session_unset();
    session_destroy();
    header("Location: /login.php?timeout=true");
    exit;
}

// Mettre à jour l'heure de la dernière activité
$_SESSION['last_activity'] = time();

// Vérifier si l'utilisateur est connecté
function check_auth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.php");
        exit;
    }
}
