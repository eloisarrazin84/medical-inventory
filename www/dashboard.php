<?php
include 'includes/db.php';
include 'includes/auth.php';

// Nombre total de sacs médicaux
$stmt = $pdo->query("SELECT COUNT(*) AS total_sacs FROM sacs_medicaux");
$total_sacs = $stmt->fetch()['total_sacs'];

// Nombre total de médicaments
$stmt = $pdo->query("SELECT COUNT(*) AS total_medicaments FROM medicaments");
$total_medicaments = $stmt->fetch()['total_medicaments'];

// Médicaments expirés avec le nom du sac
$stmt = $pdo->query("
    SELECT medicaments.nom AS med_nom, medicaments.date_expiration, sacs_medicaux.nom AS sac_nom
    FROM medicaments
    LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id
    WHERE medicaments.date_expiration < CURDATE()
");
$details_medicaments_expires = $stmt->fetchAll();

// Médicaments proches de l'expiration avec le nom du sac
$stmt = $pdo->query("
    SELECT medicaments.nom AS med_nom, medicaments.date_expiration, sacs_medicaux.nom AS sac_nom
    FROM medicaments
    LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id
    WHERE medicaments.date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
");
$details_medicaments_proches_expiration = $stmt->fetchAll();

// Détails des médicaments expirés
$stmt = $pdo->query("SELECT nom, date_expiration FROM medicaments WHERE date_expiration < CURDATE()");
$details_medicaments_expires = $stmt->fetchAll();

// Détails des médicaments proches de l'expiration
$stmt = $pdo->query("SELECT nom, date_expiration FROM medicaments WHERE date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
$details_medicaments_proches_expiration = $stmt->fetchAll();

// Détails des incidents
$stmt = $pdo->query("SELECT statut, COUNT(*) AS total FROM incidents GROUP BY statut");
$incidents = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$non_resolus = $incidents['Non Résolu'] ?? 0;
$en_cours = $incidents['En Cours'] ?? 0;
$resolus = $incidents['Résolu'] ?? 0;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"> <!-- Animate.css -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"> <!-- AOS -->
<style>
    /* Style pour le menu */
    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1030;
        background-color: rgba(0, 0, 0, 0.8); /* Transparence avec fond noir */
    }

    .navbar-brand img {
        height: 50px;
    }

    .btn {
        border-radius: 30px; /* Boutons arrondis */
        font-weight: bold; /* Texte en gras */
        transition: all 0.3s ease-in-out; /* Animation fluide */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Ombre légère */
    }

    .btn:hover {
        transform: translateY(-3px); /* Effet de levée */
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2); /* Ombre plus forte */
        color: #fff !important; /* Texte blanc au survol */
    }

    /* Style pour le tableau de bord */
    .card {
        border-radius: 15px; /* Bords arrondis pour les cartes */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Ombre légère */
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* Animation des cartes */
    }

    .card:hover {
        transform: scale(1.05); /* Agrandissement au survol */
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2); /* Ombre plus forte au survol */
    }

    .card-icon {
        font-size: 2rem; /* Taille des icônes dans les cartes */
        margin-right: 10px;
    }

    /* Style pour les notifications */
    .alert {
        border-radius: 10px; /* Bords arrondis pour les alertes */
        animation: fadeIn 0.5s ease-in-out; /* Animation au chargement */
    }

    /* Animation pour les notifications */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Marges pour compenser le menu fixe */
    body {
        padding-top: 80px; /* Espace sous le menu */
    }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }
        .card-icon {
            font-size: 2rem;
            margin-right: 10px;
        }
        .alert {
            margin-bottom: 20px;
        }
        .row {
            margin-top: 20px;
        }
</style>
</head>
<body>

<!-- Inclure le menu -->
<?php include 'menus/menu_dashboard.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4">Tableau de Bord</h1>

    <!-- Cartes de Statistiques -->
    <div class="row">
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
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-hourglass-half card-icon"></i>
                    <div>
                        <h5 class="card-title">Incidents En Cours</h5>
                        <p class="card-text"><?= $en_cours ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-check-circle card-icon"></i>
                    <div>
                        <h5 class="card-title">Incidents Résolus</h5>
                        <p class="card-text"><?= $resolus ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    <h2 class="mt-4">Notifications</h2>
   <!-- Médicaments Expirés -->
<?php if (!empty($details_medicaments_expires)): ?>
    <div class="alert alert-danger">
        <h4>Médicaments Expirés</h4>
        <ul>
            <?php foreach ($details_medicaments_expires as $med): ?>
                <li>
                    <?= htmlspecialchars($med['med_nom']) ?> - Expiré le <?= htmlspecialchars($med['date_expiration']) ?>
                    <strong>(Sac : <?= htmlspecialchars($med['sac_nom'] ?? 'Non spécifié') ?>)</strong>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Médicaments Proches de l'Expiration -->
<?php if (!empty($details_medicaments_proches_expiration)): ?>
    <div class="alert alert-warning">
        <h4>Médicaments Proches de l'Expiration</h4>
        <ul>
            <?php foreach ($details_medicaments_proches_expiration as $med): ?>
                <li>
                    <?= htmlspecialchars($med['med_nom']) ?> - Expire le <?= htmlspecialchars($med['date_expiration']) ?>
                    <strong>(Sac : <?= htmlspecialchars($med['sac_nom'] ?? 'Non spécifié') ?>)</strong>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
</div>
</div>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000, // Durée de l'animation (en ms)
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
</body>
</html>
