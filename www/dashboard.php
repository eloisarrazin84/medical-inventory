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
    <style>
    body {
    background-color: #f9f9f9;
}

.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 1rem;
    background-color: rgba(0, 0, 0, 0.9);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.navbar-brand {
    display: flex;
    align-items: center;
}

.navbar-brand img {
    height: 40px;
    margin-right: 10px;
}

.navbar-nav {
    display: flex;
    align-items: center;
    gap: 15px;
}

.nav-item {
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-link, .dropdown-toggle, .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px 15px;
    border-radius: 5px;
    color: #f8f9fa; /* Texte visible */
    font-size: 1rem;
    text-decoration: none;
    background-color: transparent;
    transition: all 0.3s ease;
}

.nav-link:hover, .dropdown-toggle:hover, .btn:hover {
    background-color: rgba(255, 255, 255, 0.2);
    color: #fff;
}

.dropdown-menu {
    min-width: 150px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    color: #333;
}

.dropdown-item {
    padding: 10px;
    font-size: 0.9rem;
    color: #333;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #007bff;
}

.container {
    max-width: 900px; /* Limitation de la largeur */
    margin: 0 auto;
    padding: 20px;
}

.card {
    border-radius: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    color: white;
    text-align: center;
    padding: 20px;
    height: 150px; /* Uniformiser les hauteurs */
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.card-icon {
    font-size: 3rem;
    margin-bottom: 10px;
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

.btn-toggle:hover {
    background-color: #e9ecef;
}

.table-responsive {
    margin-top: 20px;
}

.table-custom th, .table-custom td {
    font-size: 14px;
    text-align: center;
    padding: 10px;
}

.table-custom tr:nth-child(even) {
    background-color: #f4f4f4;
}

.table-custom tr:hover {
    background-color: #e9ecef;
}

.badge {
    font-size: 14px;
    padding: 8px 12px;
    border-radius: 20px;
}

.badge-warning {
    background-color: #ffc107;
    color: #fff;
}

.badge-danger {
    background-color: #dc3545;
    color: #fff;
}

@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        align-items: flex-start;
        padding: 10px;
    }

    .navbar-nav {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        width: 100%;
    }

    .nav-item {
        width: 100%;
        justify-content: flex-start;
    }

    .card {
        margin-bottom: 15px;
    }

    .btn-toggle {
        font-size: 0.9rem;
    }

    .table-custom th, .table-custom td {
        font-size: 12px;
        padding: 8px;
    }

    .badge {
        font-size: 12px;
    }
}
    </style>
</head>
<body>
<?php include 'menus/menu_dashboard.php'; ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Tableau de Bord</h1>
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

    <h2 class="text-warning mt-4">Médicaments Proches de l'Expiration</h2>
    <button class="btn-toggle" data-bs-toggle="collapse" data-bs-target="#medicamentsProches">
        Afficher / Cacher
    </button>
    <div class="collapse" id="medicamentsProches">
        <div class="table-responsive">
            <table class="table table-bordered table-custom">
                <thead>
                    <tr>
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
                                <td><span class="badge badge-warning"><?= htmlspecialchars($med['date_expiration']) ?></span></td>
                                <td><?= htmlspecialchars($med['sac_nom']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">Aucun médicament proche de l'expiration.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <h2 class="text-danger mt-4">Médicaments Expirés</h2>
    <button class="btn-toggle" data-bs-toggle="collapse" data-bs-target="#medicamentsExpires">
        Afficher / Cacher
    </button>
    <div class="collapse" id="medicamentsExpires">
        <div class="table-responsive">
            <table class="table table-bordered table-custom">
                <thead>
                    <tr>
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
                                <td><span class="badge badge-danger"><?= htmlspecialchars($med['date_expiration']) ?></span></td>
                                <td><?= htmlspecialchars($med['sac_nom']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">Aucun médicament expiré.</td>
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
