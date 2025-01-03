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
