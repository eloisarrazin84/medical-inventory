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

// Nombre total de lots
$stmt = $pdo->query("SELECT COUNT(*) AS total_lots FROM lots");
$total_lots = $stmt->fetch()['total_lots'];

// Nombre total de consommables
$stmt = $pdo->query("SELECT COUNT(*) AS total_consommables FROM consommables");
$total_consommables = $stmt->fetch()['total_consommables'];

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

// Consommables proches de l'expiration
$stmt = $pdo->query("
    SELECT consommables.nom AS cons_nom, consommables.date_expiration, lots.nom AS lot_nom, sacs_medicaux.nom AS sac_nom
    FROM consommables
    LEFT JOIN lots ON consommables.lot_id = lots.id
    LEFT JOIN sacs_medicaux ON lots.sac_id = sacs_medicaux.id
    WHERE consommables.date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
");
$details_consommables_proches_expiration = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consommables expirés
$stmt = $pdo->query("
    SELECT consommables.nom AS cons_nom, consommables.date_expiration, lots.nom AS lot_nom, sacs_medicaux.nom AS sac_nom
    FROM consommables
    LEFT JOIN lots ON consommables.lot_id = lots.id
    LEFT JOIN sacs_medicaux ON lots.sac_id = sacs_medicaux.id
    WHERE consommables.date_expiration < CURDATE()
");
$details_consommables_expires = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <link href="css/dashboard.css" rel="stylesheet">
</head>
<body>
<?php include 'menus/menu_dashboard.php'; ?>
<div class="page-content">
<div class="container mt-5">
    <h1 class="text-center mb-4">Tableau de Bord</h1>
    <div class="row text-center g-3">
        <!-- Carte : Total Sacs Médicaux -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-primary" onclick="toggleDetails('sacsDetails')">
                <i class="fas fa-briefcase-medical card-icon"></i>
                <h5>Total Sacs Médicaux</h5>
                <p><?= $total_sacs ?></p>
            </div>
        </div>

        <!-- Carte : Total Lots -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-success" onclick="toggleDetails('lotsDetails')">
                <i class="fas fa-box-open card-icon"></i>
                <h5>Total Lots</h5>
                <p><?= $total_lots ?></p>
            </div>
        </div>

        <!-- Carte : Total Médicaments -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-info" onclick="toggleDetails('medicamentsDetails')">
                <i class="fas fa-pills card-icon"></i>
                <h5>Total Médicaments</h5>
                <p><?= $total_medicaments ?></p>
            </div>
        </div>

        <!-- Carte : Médicaments Proches Expiration -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-warning" onclick="toggleDetails('prochesMedicamentsDetails')">
                <i class="fas fa-exclamation-circle card-icon"></i>
                <h5>Médicaments Proches Expiration</h5>
                <p><?= count($details_medicaments_proches_expiration) ?></p>
            </div>
        </div>

        <!-- Carte : Médicaments Expirés -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-danger" onclick="toggleDetails('expiresMedicamentsDetails')">
                <i class="fas fa-times-circle card-icon"></i>
                <h5>Médicaments Expirés</h5>
                <p><?= count($details_medicaments_expires) ?></p>
            </div>
        </div>

        <!-- Carte : Consommables Proches Expiration -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-warning" onclick="toggleDetails('prochesConsommablesDetails')">
                <i class="fas fa-clock card-icon"></i>
                <h5>Consommables Proches Expiration</h5>
                <p><?= count($details_consommables_proches_expiration) ?></p>
            </div>
        </div>

        <!-- Carte : Consommables Expirés -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-danger" onclick="toggleDetails('expiresConsommablesDetails')">
                <i class="fas fa-trash-alt card-icon"></i>
                <h5>Consommables Expirés</h5>
                <p><?= count($details_consommables_expires) ?></p>
            </div>
        </div>
    </div>

    <!-- Sections Détails -->
    <div class="details" id="prochesMedicamentsDetails" style="display: none;">
        <h3>Médicaments Proches de l'Expiration</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Date d'Expiration</th>
                    <th>Sac</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details_medicaments_proches_expiration as $med): ?>
                    <tr>
                        <td><?= htmlspecialchars($med['med_nom']) ?></td>
                        <td><?= htmlspecialchars($med['date_expiration']) ?></td>
                        <td><?= htmlspecialchars($med['sac_nom']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="details" id="expiresMedicamentsDetails" style="display: none;">
        <h3>Médicaments Expirés</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Date d'Expiration</th>
                    <th>Sac</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details_medicaments_expires as $med): ?>
                    <tr>
                        <td><?= htmlspecialchars($med['med_nom']) ?></td>
                        <td><?= htmlspecialchars($med['date_expiration']) ?></td>
                        <td><?= htmlspecialchars($med['sac_nom']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<div class="details" id="prochesConsommablesDetails" style="display: none;">
    <h3>Consommables Proches de l'Expiration</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Date d'Expiration</th>
                <th>Lot</th>
                <th>Sac</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details_consommables_proches_expiration as $cons): ?>
                <tr>
                    <td><?= htmlspecialchars($cons['cons_nom']) ?></td>
                    <td><?= htmlspecialchars($cons['date_expiration']) ?></td>
                    <td><?= htmlspecialchars($cons['lot_nom']) ?></td>
                    <td><?= htmlspecialchars($cons['sac_nom']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="details" id="expiresConsommablesDetails" style="display: none;">
    <h3>Consommables Expirés</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Date d'Expiration</th>
                <th>Lot</th>
                <th>Sac</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details_consommables_expires as $cons): ?>
                <tr>
                    <td><?= htmlspecialchars($cons['cons_nom']) ?></td>
                    <td><?= htmlspecialchars($cons['date_expiration']) ?></td>
                    <td><?= htmlspecialchars($cons['lot_nom']) ?></td>
                    <td><?= htmlspecialchars($cons['sac_nom']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000 });
</script>
    <script>
    function toggleDetails(id) {
        const sections = document.querySelectorAll('.details');
        sections.forEach(section => {
            section.style.display = 'none';
        });
        const selectedSection = document.getElementById(id);
        if (selectedSection) {
            selectedSection.style.display = 'block';
        }
    }
</script>
</body>
</html>
