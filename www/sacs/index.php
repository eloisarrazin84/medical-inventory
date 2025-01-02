<?php
include '../includes/db.php';
include '../includes/auth.php';


$stmt = $pdo->query("SELECT * FROM sacs_medicaux ORDER BY date_creation DESC");
$sacs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Liste des Sacs Médicaux</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
<!-- Inclure le menu -->
<?php include '../menus/menu_sacs.php'; ?>
<div class="container mt-5">
    <h1>Liste des Sacs Médicaux</h1>
    <a href="add.php" class="btn btn-primary mb-3">Ajouter un sac</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Date de création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sacs as $sac): ?>
                <tr>
                    <td><?= htmlspecialchars($sac['nom']) ?></td>
                    <td><?= htmlspecialchars($sac['description']) ?></td>
                    <td><?= $sac['date_creation'] ?></td>
                    <td>
                        <a href="edit.php?id=<?= $sac['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                        <a href="delete.php?id=<?= $sac['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer ce sac ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
