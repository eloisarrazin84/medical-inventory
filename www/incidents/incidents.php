<?php
include '../includes/db.php';
include '../includes/auth.php';

$stmt = $pdo->query("SELECT * FROM incidents ORDER BY date_signalement DESC");
$incidents = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Gestion des Incidents</title>
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
    <h1>Suivi des Incidents</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Type</th>
                <th>Référence</th>
                <th>Description</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($incidents as $incident): ?>
                <tr>
                    <td><?= htmlspecialchars($incident['type_incident']) ?></td>
                    <td><?= htmlspecialchars($incident['reference_id']) ?></td>
                    <td><?= htmlspecialchars($incident['description']) ?></td>
                    <td><?= htmlspecialchars($incident['statut']) ?></td>
                    <td><?= htmlspecialchars($incident['date_signalement']) ?></td>
                    <td>
                        <a href="changer_statut.php?id=<?= $incident['id'] ?>&statut=En Cours" class="btn btn-warning btn-sm">En Cours</a>
                        <a href="changer_statut.php?id=<?= $incident['id'] ?>&statut=Résolu" class="btn btn-success btn-sm">Résolu</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
