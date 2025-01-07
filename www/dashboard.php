<?php
include 'includes/db.php';
include 'includes/auth.php';
include 'session_manager.php';

// Vérifiez si l'utilisateur est connecté
check_auth();
// Nombre total de sacs médicaux
$stmt = $pdo->query("SELECT COUNT(*) AS total_sacs FROM sacs_medicaux");
$total_sacs = $stmt->fetch()['total_sacs'];

// Nombre total de médicaments
$stmt = $pdo->query("SELECT COUNT(*) AS total_medicaments FROM medicaments");
$total_medicaments = $stmt->fetch()['total_medicaments'];

// Médicaments expirés
$stmt = $pdo->query("
    SELECT medicaments.nom AS med_nom, medicaments.date_expiration, sacs_medicaux.nom AS sac_nom
    FROM medicaments
    LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id
    WHERE medicaments.date_expiration < CURDATE()
");
$details_medicaments_expires = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Médicaments proches de l'expiration
$stmt = $pdo->query("
    SELECT medicaments.nom AS med_nom, medicaments.date_expiration, sacs_medicaux.nom AS sac_nom
    FROM medicaments
    LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id
    WHERE medicaments.date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
");
$details_medicaments_proches_expiration = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistiques sur les incidents
$stmt = $pdo->query("SELECT statut, COUNT(*) AS total FROM incidents GROUP BY statut");
$incidents = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$non_resolus = $incidents['Non Résolu'] ?? 0;
$en_cours = $incidents['En Cours'] ?? 0;
$resolus = $incidents['Résolu'] ?? 0;

// Statistiques sur les rapports
$stmt = $pdo->query("SELECT COUNT(*) AS total_rapports FROM rapports_utilisation");
$total_rapports = $stmt->fetch()['total_rapports'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Tableau de Bord</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
    /* Général */
    body {
        background-color: #f9f9f9;
    }

    .navbar {
        position: sticky;
        top: 0;
        z-index: 1030;
        background-color: rgba(0, 0, 0, 0.9);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand img {
        height: 50px;
    }

    .card {
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(240, 240, 240, 0.9));
        color: white;
        text-align: center;
        padding: 20px;
    }

    .card:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .card-icon {
        font-size: 3rem;
        margin-bottom: 10px;
        display: inline-block;
    }

    .card.bg-primary {
        background: linear-gradient(135deg, #4a90e2, #007bff);
    }

    .card.bg-success {
        background: linear-gradient(135deg, #3fbf61, #28a745);
    }

    .card.bg-info {
        background: linear-gradient(135deg, #56c2e6, #17a2b8);
    }

    .card.bg-danger {
        background: linear-gradient(135deg, #e57373, #dc3545);
    }

    .card h5 {
        font-size: 1.25rem;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .card p {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0;
    }

    @media (max-width: 768px) {
        .card {
            margin-bottom: 20px;
        }

    .btn-toggle {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f8f9fa;
        padding: 12px;
        border: none;
        width: 100%;
        text-align: left;
        font-size: 1rem;
        font-weight: bold;
        color: #007bff;
        border-radius: 10px;
        margin-bottom: 10px;
        transition: background-color 0.3s ease;
        cursor: pointer;
    }

    .btn-toggle i {
        transition: transform 0.3s ease;
    }

    .btn-toggle.collapsed i {
        transform: rotate(-90deg);
    }

    .btn-toggle:hover {
        background-color: #e9ecef;
    }

    .section-title {
        margin-top: 40px;
        font-weight: bold;
        text-transform: uppercase;
        color: #333;
        font-size: 1.5rem;
    }

    .table-responsive {
        margin-top: 20px;
    }

    @media (max-width: 768px) {

        .btn-toggle {
            font-size: 0.9rem;
        }
    }
</style>
</head>
<body>
<!-- Menu -->
<?php include 'menus/menu_dashboard.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4 text-center">Tableau de Bord</h1>

<!-- Cartes avec le nouveau design -->
<div class="row text-center g-3">
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card bg-primary">
            <i class="fas fa-briefcase-medical card-icon"></i>
            <h5>Total Sacs Médicaux</h5>
            <p><?= $total_sacs ?></p>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card bg-success">
            <i class="fas fa-pills card-icon"></i>
            <h5>Total Médicaments</h5>
            <p><?= $total_medicaments ?></p>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card bg-info">
            <i class="fas fa-file-alt card-icon"></i>
            <h5>Total Rapports</h5>
            <p><?= $total_rapports ?></p>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card bg-danger">
            <i class="fas fa-times-circle card-icon"></i>
            <h5>Incidents Non Résolus</h5>
            <p><?= $non_resolus ?></p>
        </div>
    </div>
</div>
        </div>
    </div>

<!-- Affichage des sections avec les boutons améliorés -->
<h2 class="section-title text-warning">Médicaments Proches de l'Expiration</h2>
<button class="btn-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#medicamentsProches" aria-expanded="false">
    <span>Afficher / Cacher</span>
    <i class="fas fa-chevron-down"></i>
</button>
<div class="collapse" id="medicamentsProches">
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
                        <tr>
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
</div>

<h2 class="section-title text-danger">Médicaments Expirés</h2>
<button class="btn-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#medicamentsExpires" aria-expanded="false">
    <span>Afficher / Cacher</span>
    <i class="fas fa-chevron-down"></i>
</button>
<div class="collapse" id="medicamentsExpires">
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
                        <tr>
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
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000 });
</script>
</body>
</html>
