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
/* Style Général */
body {
    background-color: #f4f7fa;
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
}

/* Style pour le menu */
.navbar {
    position: sticky;
    top: 0;
    z-index: 1030;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    padding: 10px 20px;
}
.navbar-brand img {
    height: 40px;
}
.navbar .btn {
    border: none;
    border-radius: 30px;
    padding: 10px 15px;
    color: white;
    background-color: #007bff;
    transition: background-color 0.3s ease, transform 0.3s ease;
}
.navbar .btn:hover {
    background-color: #0056b3;
    transform: scale(1.05);
}

/* Style des Cartes */
.card {
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    text-align: center;
    cursor: pointer;
    background-color: white;
    padding: 20px;
    margin-bottom: 15px;
}
.card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}
.card-icon {
    font-size: 2.5rem;
    margin-bottom: 10px;
    color: #007bff;
}
.card h5 {
    font-size: 1.1rem;
    margin-bottom: 5px;
}
.card p {
    font-size: 1.5rem;
    margin: 0;
    color: #333;
}

/* Couleurs des Cartes */
.card.bg-primary {
    background: linear-gradient(135deg, #4a90e2, #007bff);
    color: white;
}
.card.bg-success {
    background: linear-gradient(135deg, #3fbf61, #28a745);
    color: white;
}
.card.bg-info {
    background: linear-gradient(135deg, #56c2e6, #17a2b8);
    color: white;
}
.card.bg-danger {
    background: linear-gradient(135deg, #e57373, #dc3545);
    color: white;
}
.card.bg-warning {
    background: linear-gradient(135deg, #ffc107, #ffcc00);
    color: black;
}

/* Boutons de bascule */
.btn-toggle {
    background-color: #f4f7fa;
    color: #007bff;
    padding: 10px 15px;
    font-size: 1rem;
    font-weight: bold;
    text-align: left;
    border: none;
    border-radius: 8px;
    width: 100%;
    transition: background-color 0.3s ease;
}
.btn-toggle:hover {
    background-color: #e9ecef;
}

/* Tableaux */
.table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
    border: 1px solid #ddd;
}
.table th, .table td {
    text-align: center;
    padding: 12px;
    font-size: 0.9rem;
}
.table th {
    background-color: #f4f4f4;
    color: #333;
}
.table tr:nth-child(even) {
    background-color: #f9f9f9;
}
.table tr:hover {
    background-color: #e9ecef;
}

/* Responsive */
@media (max-width: 768px) {
    .card-icon {
        font-size: 2rem;
    }
    .card h5 {
        font-size: 1rem;
    }
    .card p {
        font-size: 1.3rem;
    }
    .table th, .table td {
        font-size: 0.8rem;
    }
    .navbar .btn {
        font-size: 0.9rem;
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
