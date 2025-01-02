<?php
include '../includes/db.php';

// Récupérer l'ID du sac depuis l'URL
$sac_id = $_GET['sac_id'];

// Récupérer les informations du sac
$stmt = $pdo->prepare("SELECT * FROM sacs_medicaux WHERE id = ?");
$stmt->execute([$sac_id]);
$sac = $stmt->fetch();

// Récupérer les médicaments associés au sac
$stmt = $pdo->prepare("SELECT * FROM medicaments WHERE sac_id = ?");
$stmt->execute([$sac_id]);
$medicaments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Médicaments - <?= htmlspecialchars($sac['nom']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Médicaments - <?= htmlspecialchars($sac['nom']) ?></h1>
    <a href="../sacs/index.php" class="btn btn-secondary mb-3">Retour aux sacs</a>
    <a href="add.php?sac_id=<?= $sac_id ?>" class="btn btn-primary mb-3">Ajouter un médicament</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Quantité</th>
                <th>Date d'expiration</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($medicaments as $med): ?>
                <tr>
                    <td><?= htmlspecialchars($med['nom']) ?></td>
                    <td><?= htmlspecialchars($med['description']) ?></td>
                    <td><?= $med['quantite'] ?></td>
                    <td><?= $med['date_expiration'] ?></td>
                    <td>
                        <a href="edit.php?id=<?= $med['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                        <a href="delete.php?id=<?= $med['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer ce médicament ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
