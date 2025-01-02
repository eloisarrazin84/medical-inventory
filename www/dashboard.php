<?php
include 'includes/db.php';

// Nombre total de sacs médicaux
$stmt = $pdo->query("SELECT COUNT(*) AS total_sacs FROM sacs_medicaux");
$total_sacs = $stmt->fetch()['total_sacs'];

// Nombre total de médicaments
$stmt = $pdo->query("SELECT COUNT(*) AS total_medicaments FROM medicaments");
$total_medicaments = $stmt->fetch()['total_medicaments'];

// Médicaments expirés
$stmt = $pdo->query("SELECT COUNT(*) AS medicaments_expires FROM medicaments WHERE date_expiration < CURDATE()");
$medicaments_expires = $stmt->fetch()['medicaments_expires'];

// Médicaments proches de l'expiration (dans les 30 jours)
$stmt = $pdo->query("SELECT COUNT(*) AS medicaments_proches_expiration FROM medicaments WHERE date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
$medicaments_proches_expiration = $stmt->fetch()['medicaments_proches_expiration'];

// Détails des médicaments expirés
$stmt = $pdo->query("SELECT nom, date_expiration FROM medicaments WHERE date_expiration < CURDATE()");
$details_medicaments_expires = $stmt->fetchAll();

// Détails des médicaments proches de l'expiration
$stmt = $pdo->query("SELECT nom, date_expiration FROM medicaments WHERE date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
$details_medicaments_proches_expiration = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Tableau de Bord</h1>

    <!-- Notifications -->
    <h2>Notifications</h2>

    <!-- Médicaments expirés -->
    <?php if (count($details_medicaments_expires) > 0): ?>
        <div class="alert alert-danger">
            <h4>Médicaments Expirés</h4>
            <ul>
                <?php foreach ($details_medicaments_expires as $med): ?>
                    <li>
                        <?= htmlspecialchars($med['nom']) ?> - Expiré le <?= htmlspecialchars($med['date_expiration']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Médicaments proches de l'expiration -->
    <?php if (count($details_medicaments_proches_expiration) > 0): ?>
        <div class="alert alert-warning">
            <h4>Médicaments Proches de l'Expiration</h4>
            <ul>
                <?php foreach ($details_medicaments_proches_expiration as $med): ?>
                    <li>
                        <?= htmlspecialchars($med['nom']) ?> - Expire le <?= htmlspecialchars($med['date_expiration']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Aucune notification -->
    <?php if (count($details_medicaments_expires) === 0 && count($details_medicaments_proches_expiration) === 0): ?>
        <div class="alert alert-success">
            Aucun médicament expiré ou proche de l'expiration.
        </div>
    <?php endif; ?>

    <!-- Cartes de statistiques -->
    <div class="row mt-5">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Sacs Médicaux</h5>
                    <p class="card-text"><?= $total_sacs ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Médicaments</h5>
                    <p class="card-text"><?= $total_medicaments ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Médicaments Expirés</h5>
                    <p class="card-text"><?= $medicaments_expires ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Expiration Proche</h5>
                    <p class="card-text"><?= $medicaments_proches_expiration ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des médicaments proches de l'expiration -->
    <h2 class="mt-5">Médicaments Proches de l'Expiration</h2>
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
            <?php
            $stmt = $pdo->query("SELECT * FROM medicaments WHERE date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) ORDER BY date_expiration ASC");
            $medicaments = $stmt->fetchAll();
            if (count($medicaments) > 0):
                foreach ($medicaments as $med): ?>
                    <tr>
                        <td><?= htmlspecialchars($med['nom']) ?></td>
                        <td><?= htmlspecialchars($med['description'] ?? 'Aucune description') ?></td>
                        <td><?= htmlspecialchars($med['quantite']) ?></td>
                        <td><?= htmlspecialchars($med['date_expiration']) ?></td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan="4" class="text-center">Aucun médicament proche de l'expiration.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
