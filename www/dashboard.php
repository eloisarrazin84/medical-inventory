<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Tableau de Bord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .card-icon {
            font-size: 2.5rem;
            margin-right: 10px;
        }

        .badge {
            font-size: 0.9rem;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .floating-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }

        .floating-btn:hover {
            background-color: #0056b3;
            transform: scale(1.1);
        }

        .section-title {
            margin-top: 40px;
            margin-bottom: 20px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4 text-center">Tableau de Bord</h1>

    <!-- Statistiques sous forme de cartes -->
    <div class="row text-center">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-briefcase-medical card-icon"></i>
                    <div>
                        <h5 class="card-title">Total Sacs Médicaux</h5>
                        <p class="card-text"><?= $total_sacs ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
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
            <div class="card text-white bg-info">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-file-alt card-icon"></i>
                    <div>
                        <h5 class="card-title">Total Rapports</h5>
                        <p class="card-text"><?= $total_rapports ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-times-circle card-icon"></i>
                    <div>
                        <h5 class="card-title">Incidents Non Résolus</h5>
                        <p class="card-text"><?= $non_resolus ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Médicaments proches de l'expiration -->
    <h2 class="section-title text-warning">Médicaments Proches de l'Expiration</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr class="table-warning">
                    <th>Nom du Médicament</th>
                    <th>Date d'Expiration</th>
                    <th>Nom du Sac</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($details_medicaments_proches_expiration)): ?>
                    <?php foreach ($details_medicaments_proches_expiration as $med): ?>
                        <tr class="table-warning">
                            <td><?= htmlspecialchars($med['med_nom']) ?></td>
                            <td><span class="badge bg-warning"><?= htmlspecialchars($med['date_expiration']) ?></span></td>
                            <td><?= htmlspecialchars($med['sac_nom']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">Aucun médicament proche de l'expiration.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Section Médicaments expirés -->
    <h2 class="section-title text-danger">Médicaments Expirés</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr class="table-danger">
                    <th>Nom du Médicament</th>
                    <th>Date d'Expiration</th>
                    <th>Nom du Sac</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($details_medicaments_expires)): ?>
                    <?php foreach ($details_medicaments_expires as $med): ?>
                        <tr class="table-danger">
                            <td><?= htmlspecialchars($med['med_nom']) ?></td>
                            <td><span class="badge bg-danger"><?= htmlspecialchars($med['date_expiration']) ?></span></td>
                            <td><?= htmlspecialchars($med['sac_nom']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">Aucun médicament expiré.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bouton flottant -->
<a href="add_medicament.php" class="floating-btn" title="Ajouter un médicament">
    <i class="fas fa-plus"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Ajouter des graphiques si nécessaire
</script>
</body>
</html>
