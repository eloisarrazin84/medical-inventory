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
    <style>
/* Style Général */
body {
    background-color: #f4f7fa;
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
}

/* Style Général */
.navbar {
    background: linear-gradient(135deg, #004aad, #007bff);
    padding: 15px 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 1030;
}

/* Logo */
.navbar-brand img {
    height: 50px;
    transition: transform 0.3s ease;
}
.navbar-brand img:hover {
    transform: scale(1.1);
}

/* Boutons */
.navbar .btn {
    font-weight: bold;
    font-size: 1rem;
    border-radius: 30px;
    padding: 10px 20px;
    text-transform: uppercase;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    color: white !important;
}
.navbar .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
}
.btn-primary {
    background-color: #007bff;
    border: none;
}
.btn-primary:hover {
    background-color: #0056b3;
}
.btn-success {
    background-color: #28a745;
    border: none;
}
.btn-success:hover {
    background-color: #218838;
}
.btn-danger {
    background-color: #dc3545;
    border: none;
}
.btn-danger:hover {
    background-color: #c82333;
}

/* Dropdown Menu */
.dropdown-menu {
    border-radius: 10px;
    border: none;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}
.dropdown-item {
    padding: 10px 15px;
    font-size: 0.9rem;
    transition: background-color 0.3s ease;
}
.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #007bff;
}

/* Toggler (Mobile View) */
.navbar-toggler {
    border: none;
}
.navbar-toggler-icon {
    color: white;
    background-color: white;
    border-radius: 3px;
    padding: 5px;
}
.navbar-toggler:focus {
    outline: none;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .navbar .btn {
        font-size: 0.9rem;
        padding: 8px 15px;
    }
     .dropdown-menu {
        width: 100%; /* Le menu occupe toute la largeur */
        text-align: center; /* Centrer le texte */
        padding: 10px; /* Ajouter de l'espace interne */
    }

    .dropdown-item {
        font-size: 1rem; /* Agrandir légèrement la taille du texte */
        padding: 12px 20px; /* Ajouter de l'espace entre les items */
        border-bottom: 1px solid #e9ecef; /* Ligne pour séparer chaque item */
        display: flex; /* Affichage en flex */
        align-items: center; /* Alignement vertical des icônes et du texte */
        justify-content: flex-start; /* Alignement gauche */
    }

    .dropdown-item:last-child {
        border-bottom: none; /* Supprimer la bordure du dernier élément */
    }

    .dropdown-item i {
        margin-right: 10px; /* Espace entre l'icône et le texte */
        font-size: 1.2rem; /* Taille des icônes */
        color: #007bff; /* Couleur des icônes */
    }

    .dropdown-menu.show {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Ajouter une ombre */
        border-radius: 10px; /* Arrondir les coins */
    }
}

/* Cartes */
.card {
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
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
    font-size: 3rem;
    margin-bottom: 10px;
    color: white;
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

/* Responsive Design */
@media (max-width: 768px) {
    .navbar-nav .btn {
        margin-bottom: 10px;
        width: 100%;
    }
    .navbar-nav .dropdown-menu {
        width: 100%;
    }
}
    .card-icon {
        font-size: 2rem;
    }
    .card h5 {
        font-size: 1rem;
    }
    .table th, .table td {
        font-size: 0.8rem;
    }
}
    </style>
</head>
<body>
<?php include 'menus/menu_dashboard.php'; ?>

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
