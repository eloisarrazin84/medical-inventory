<?php
include '../includes/db.php';

$sac_id = $_GET['sac_id'] ?? null;

// Vérifier si le sac existe
$stmt = $pdo->prepare("SELECT * FROM sacs_medicaux WHERE id = ?");
$stmt->execute([$sac_id]);
$sac = $stmt->fetch();

if (!$sac) {
    die('Sac médical introuvable.');
}

// Récupérer les médicaments du sac
$stmt = $pdo->prepare("SELECT * FROM medicaments WHERE sac_id = ?");
$stmt->execute([$sac_id]);
$medicaments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Inventaire - <?= htmlspecialchars($sac['nom']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Inventaire : <?= htmlspecialchars($sac['nom']) ?></h1>
    <a href="../dashboard.php" class="btn btn-secondary mb-3">Retour</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Quantité</th>
                <th>Date d'expiration</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($medicaments as $med): ?>
                <tr>
                    <td><?= htmlspecialchars($med['nom']) ?></td>
                    <td><?= htmlspecialchars($med['description']) ?></td>
                    <td><?= htmlspecialchars($med['quantite']) ?></td>
                    <td><?= htmlspecialchars($med['date_expiration']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
