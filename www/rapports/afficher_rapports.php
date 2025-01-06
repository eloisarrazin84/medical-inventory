<?php
include '../includes/db.php';

// Récupérer tous les rapports
$stmt = $pdo->query("
    SELECT rapports_utilisation.*, sacs_medicaux.nom AS nom_sac 
    FROM rapports_utilisation 
    JOIN sacs_medicaux ON rapports_utilisation.sac_id = sacs_medicaux.id
    ORDER BY date_rapport DESC
");
$rapports = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Rapports d'Utilisation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Rapports d'Utilisation</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sac</th>
                <th>Nom de l'Événement</th>
                <th>Utilisateur</th>
                <th>Matériel Utilisé</th>
                <th>Observations</th>
                <th>Date de Saisie</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rapports as $rapport): ?>
                <tr>
                    <td><?= htmlspecialchars($rapport['nom_sac']) ?></td>
                    <td><?= htmlspecialchars($rapport['nom_evenement']) ?></td>
                    <td><?= htmlspecialchars($rapport['utilisateur']) ?></td>
                    <td><?= nl2br(htmlspecialchars($rapport['materiels_utilises'])) ?></td>
                    <td><?= nl2br(htmlspecialchars($rapport['observations'])) ?></td>
                    <td><?= htmlspecialchars($rapport['date_saisie']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
