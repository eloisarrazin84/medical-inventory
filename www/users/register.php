<?php
require '../vendor/autoload.php'; // Assurez-vous que le chemin est correct

include '../includes/db.php';
include '../includes/send_email.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $role = $_POST['role'];

    try {
        // Insertion de l'utilisateur dans la base de données
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$email, $hashedPassword, $role]);

        // Préparation de l'e-mail
        $sujet = "Bienvenue sur notre plateforme";
        $message = "
            Bonjour,

            Un compte a été créé pour vous sur notre plateforme.

            Vos informations de connexion sont les suivantes :
            Email : $email
            Mot de passe : $password

            Veuillez vous connecter et changer votre mot de passe dès que possible.

            Cordialement,
            L'équipe.
        ";

        // Appel de la fonction d'envoi d'e-mail
        if (sendEmail($email, $sujet, $message)) {
            // Redirection avec message de succès
            header('Location: manage_users.php?message=Utilisateur ajouté avec succès et email envoyé');
        } else {
            header('Location: manage_users.php?message=Utilisateur ajouté mais échec de l\'envoi de l\'email');
        }
        exit;
    } catch (PDOException $e) {
        die('Erreur lors de la création de l\'utilisateur : ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Ajouter un Utilisateur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"> <!-- Animate.css -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"> <!-- AOS -->
<style>
    /* Style pour le menu */
    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1030;
        background-color: rgba(0, 0, 0, 0.8); /* Transparence avec fond noir */
    }

    .navbar-brand img {
        height: 50px;
    }

    .btn {
        border-radius: 30px; /* Boutons arrondis */
        font-weight: bold; /* Texte en gras */
        transition: all 0.3s ease-in-out; /* Animation fluide */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Ombre légère */
    }

    .btn:hover {
        transform: translateY(-3px); /* Effet de levée */
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2); /* Ombre plus forte */
        color: #fff !important; /* Texte blanc au survol */
    }
</style>
</head>
<body>
<div class="container mt-5">
    <h1>Ajouter un Utilisateur</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Rôle</label>
            <select class="form-select" id="role" name="role">
                <option value="user">Utilisateur</option>
                <option value="admin">Administrateur</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
</div>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000, // Durée de l'animation (en ms)
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
</body>
</html>
