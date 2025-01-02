<?php
include 'includes/db.php';
include 'includes/auth.php';

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card-icon {
            font-size: 2rem;
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Tableau de Bord</h1>

    <!-- Notifications -->
    <h2>Notifications</h2>
    <?php if (count($details_medicaments_expires) > 0): ?>
        <div class="alert alert-danger">
            <h4><i class="fas fa-exclamation-triangle"></i> Médicaments Expirés</h4>
            <ul>
                <?php foreach ($details_medicaments_expires as $med): ?>
                    <li>
                        <?= htmlspecialchars($med['nom']) ?> - Expiré le <?= htmlspecialchars($med['date_expiration']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (count($details_medicaments_proches_expiration) > 0): ?>
        <div class="alert alert-warning">
            <h4><i class="fas fa-clock"></i> Médicaments Proches de l'Expiration</h4>
            <ul>
                <?php foreach ($details_medicaments_proches_expiration as $med): ?>
                    <li>
                        <?= htmlspecialchars($med['nom']) ?> - Expire le <?= htmlspecialchars($med['date_expiration']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (count($details_medicaments_expires) === 0 && count($details_medicaments_proches_expiration) === 0): ?>
        <div class="alert alert-success">
            Aucun médicament expiré ou proche de l'expiration.
        </div>
    <?php endif; ?>

    <!-- Cartes de statistiques -->
    <div class="row mt-5">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-box-medical card-icon"></i>
                    <div>
                        <h5 class="card-title">Total Sacs Médicaux</h5>
                        <p class="card-text"><?= $total_sacs ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-pills card-icon"></i>
                    <div>
                        <h5 class="card-title">Total Médicaments</h5>
                        <p class="card-text"><?= $total_medicaments ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-exclamation-circle card-icon"></i>
                    <div>
                        <h5 class="card-title">Médicaments Expirés</h5>
                        <p class="card-text"><?= $medicaments_expires ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-clock card-icon"></i>
                    <div>
                        <h5 class="card-title">Expiration Proche</h5>
                        <p class="card-text"><?= $medicaments_proches_expiration ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique -->
    <h2 class="mt-5">Statistiques des Médicaments</h2>
    <canvas id="medicamentChart" width="400" height="200"></canvas>
    <script>
        const ctx = document.getElementById('medicamentChart').getContext('2d');
        const medicamentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total Médicaments', 'Médicaments Expirés', 'Expiration Proche'],
                datasets: [{
                    label: 'Statistiques des Médicaments',
                    data: [<?= $total_medicaments ?>, <?= $medicaments_expires ?>, <?= $medicaments_proches_expiration ?>],
                    backgroundColor: ['#28a745', '#dc3545', '#ffc107']
                }]
            }
        });
    </script>
</div>
</body>
</html>
