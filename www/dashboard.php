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
    SELECT consommables.nom AS cons_nom, consommables.date_expiration, lots.nom AS lot_nom
    FROM consommables
    LEFT JOIN lots ON consommables.lot_id = lots.id
    WHERE consommables.date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
");
$details_consommables_proches_expiration = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lots avec des consommables expirés
$stmt = $pdo->query("
    SELECT lots.nom AS lot_nom, consommables.nom AS cons_nom, consommables.date_expiration
    FROM consommables
    LEFT JOIN lots ON consommables.lot_id = lots.id
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
    <style>
 /* Style général */
body {
    background-color: #f9f9f9;
    font-family: Arial, sans-serif;
}

/* Style pour le menu */
.navbar {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1030;
    background-color: rgba(0, 0, 0, 0.8); /* Transparence avec fond noir */
    padding: 10px;
}
.navbar-brand img {
    height: 50px;
}
.navbar .btn {
    min-width: 150px;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 15px;
    border-radius: 30px;
    font-weight: bold;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease-in-out;
}
.navbar .btn i {
    margin-right: 8px;
}
.navbar .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
    color: #fff !important;
}

/* Dropdown styles */
.dropdown-menu {
    min-width: 150px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
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

/* Container and layout */
.container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

/* Card styles */
.card {
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    text-align: center;
    cursor: pointer;
    padding: 20px;
    min-height: 150px;
    color: white;
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
.card.bg-warning {
    background: linear-gradient(135deg, #ffc107, #ffcc00);
    color: black;
}

/* Toggle button */
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

/* Table styles */
.table-custom {
    margin-top: 20px;
    width: 100%;
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

/* Badge styles */
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

/* Responsive styles */
@media (max-width: 768px) {
    .card-icon {
        font-size: 2.5rem;
    }
    .card h5 {
        font-size: 1rem;
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
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-primary" onclick="toggleDetails('sacsDetails')">
                <i class="fas fa-briefcase-medical card-icon"></i>
                <h5>Total Sacs Médicaux</h5>
                <p><?= $total_sacs ?></p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-success" onclick="toggleDetails('lotsDetails')">
                <i class="fas fa-box-open card-icon"></i>
                <h5>Total Lots</h5>
                <p><?= $total_lots ?></p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-info" onclick="toggleDetails('medicamentsDetails')">
                <i class="fas fa-pills card-icon"></i>
                <h5>Total Médicaments</h5>
                <p><?= $total_medicaments ?></p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-warning" onclick="toggleDetails('prochesDetails')">
                <i class="fas fa-exclamation-circle card-icon"></i>
                <h5>Médicaments Proches Expiration</h5>
                <p><?= count($details_medicaments_proches_expiration) ?></p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-danger" onclick="toggleDetails('expiresDetails')">
                <i class="fas fa-times-circle card-icon"></i>
                <h5>Médicaments Expirés</h5>
                <p><?= count($details_medicaments_expires) ?></p>
            </div>
        </div>
    </div>

    <!-- Section Details -->
    <div class="details" id="sacsDetails" style="display: none;">
        <h3>Total Sacs Médicaux</h3>
        <!-- Ajouter un tableau ou un contenu -->
        <p>Liste des sacs médicaux...</p>
    </div>

    <div class="details" id="lotsDetails" style="display: none;">
        <h3>Total Lots</h3>
        <p>Liste des lots...</p>
    </div>

    <div class="details" id="medicamentsDetails" style="display: none;">
        <h3>Total Médicaments</h3>
        <p>Liste des médicaments...</p>
    </div>

    <div class="details" id="prochesDetails" style="display: none;">
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

    <div class="details" id="expiresDetails" style="display: none;">
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
