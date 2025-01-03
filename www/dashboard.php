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
$details_medicaments_expires = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Médicaments proches de l'expiration avec le nom du sac
$stmt = $pdo->query("
    SELECT medicaments.nom AS med_nom, medicaments.date_expiration, sacs_medicaux.nom AS sac_nom
    FROM medicaments
    LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id
    WHERE medicaments.date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
");
$details_medicaments_proches_expiration = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
    <style>

   .navbar .btn {
    min-width: 150px; /* Largeur minimale pour uniformité */
    max-width: auto; /* Laisse la largeur s'ajuster dynamiquement */
    text-align: center; /* Centre le texte */
    white-space: nowrap; /* Empêche le retour à la ligne */
    display: inline-flex; /* Permet une meilleure gestion des espaces */
    align-items: center; /* Aligne le texte et l'icône verticalement */
    justify-content: center; /* Centre le contenu horizontalement */
    padding: 10px 15px; /* Ajuste l'espacement interne */
}

.navbar .btn i {
    margin-right: 8px; /* Espace entre l'icône et le texte */
}
        .dropdown-item i {
    margin-right: 8px; /* Espace entre l'icône et le texte */
}
        .dropdown-toggle {
    border: none; /* Supprime la bordure */
    box-shadow: none; /* Supprime l'ombre */
}
.dropdown-toggle:focus {
    outline: none; /* Supprime l'effet de focus */
    box-shadow: none; /* Supprime l'ombre au focus */
}
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
        .card { border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; margin-bottom: 20px; }
        .card:hover { transform: scale(1.05); box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2); }
        .card-icon { font-size: 2rem; margin-right: 10px; }
        .alert { margin-bottom: 20px; }
        .row { margin-top: 20px; }
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

    <!-- Médicaments proches de l'expiration sous forme de tableau -->
    <h2 class="mt-4">Médicaments Proches de l'Expiration</h2>
    <table class="table table-bordered">
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
                        <td><?= htmlspecialchars($med['med_nom'] ?? 'Nom non spécifié') ?></td>
                        <td><?= htmlspecialchars($med['date_expiration'] ?? 'Date non spécifiée') ?></td>
                        <td><?= htmlspecialchars($med['sac_nom'] ?? 'Non spécifié') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">Aucun médicament proche de l'expiration.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Médicaments expirés sous forme de tableau -->
    <h2 class="mt-4">Médicaments Expirés</h2>
    <table class="table table-bordered">
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
                        <td><?= htmlspecialchars($med['med_nom'] ?? 'Nom non spécifié') ?></td>
                        <td><?= htmlspecialchars($med['date_expiration'] ?? 'Date non spécifiée') ?></td>
                        <td><?= htmlspecialchars($med['sac_nom'] ?? 'Non spécifié') ?></td>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000 });
</script>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
</body>
</html>
