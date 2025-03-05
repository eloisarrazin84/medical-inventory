<?php
include 'includes/db.php';
include 'includes/auth.php';
include 'session_manager.php';

// Vérifiez si l'utilisateur est connecté
check_auth();

// Gestion des filtres dynamiques
$filter = $_GET['filter'] ?? 'all';
$whereClause = '';

if ($filter == 'medicaments_expired') {
    $whereClause = "WHERE medicaments.date_expiration < CURDATE()";
} elseif ($filter == 'medicaments_soon') {
    $whereClause = "WHERE medicaments.date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
}

// Requêtes pour les données du tableau de bord
$stmt = $pdo->query("SELECT COUNT(*) AS total_sacs FROM sacs_medicaux");
$total_sacs = $stmt->fetch()['total_sacs'];

$stmt = $pdo->query("SELECT COUNT(*) AS total_medicaments FROM medicaments");
$total_medicaments = $stmt->fetch()['total_medicaments'];

$stmt = $pdo->query("SELECT COUNT(*) AS total_lots FROM lots");
$total_lots = $stmt->fetch()['total_lots'];

$stmt = $pdo->query("SELECT COUNT(*) AS total_consommables FROM consommables");
$total_consommables = $stmt->fetch()['total_consommables'];

$stmt = $pdo->query("SELECT medicaments.nom AS med_nom, medicaments.date_expiration, sacs_medicaux.nom AS sac_nom FROM medicaments LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id $whereClause");
$filtered_medicaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background: #007bff;
            color: white;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'menus/menu_dashboard.php'; ?>
<div class="container mt-5">
    <h1 class="text-center mb-4">Tableau de Bord</h1>
    
    <div class="row g-3 mt-4 text-center">
        <div class="col-6 col-md-3">
            <button class="btn btn-primary rounded-pill px-4 py-2"><i class="fas fa-briefcase-medical"></i> Sacs Médicaux (<?= $total_sacs ?>)</button>
        </div>
        <div class="col-6 col-md-3">
            <button class="btn btn-secondary rounded-pill px-4 py-2"><i class="fas fa-pills"></i> Médicaments (<?= $total_medicaments ?>)</button>
        </div>
        <div class="col-6 col-md-3">
            <button class="btn btn-info rounded-pill px-4 py-2"><i class="fas fa-boxes"></i> Lots (<?= $total_lots ?>)</button>
        </div>
        <div class="col-6 col-md-3">
            <button class="btn btn-success rounded-pill px-4 py-2"><i class="fas fa-box-open"></i> Consommables (<?= $total_consommables ?>)</button>
        </div>
    </div>

    <h3 class="mt-5">Détails des Médicaments</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Date d'Expiration</th>
                    <th>Sac</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filtered_medicaments as $med): ?>
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
<script>
    function applyFilter() {
        let filter = document.getElementById('filter').value;
        window.location.href = '?filter=' + filter;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
    });
</script>    
</body>
</html>
