<?php
include '../includes/db.php';
include 'includes/auth.php';


$stmt = $pdo->query("SELECT * FROM sacs_medicaux ORDER BY date_creation DESC");
$sacs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Liste des Sacs Médicaux</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
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
