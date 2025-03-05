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
    <link href="css/dashboard.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'menus/menu_usersmanage.php'; ?>
<div class="container mt-5">
    <h1 class="text-center mb-4">Tableau de Bord</h1>
    
    <!-- Filtres dynamiques -->
    <div class="mb-3 text-center">
        <label for="filter" class="form-label">Filtrer les médicaments :</label>
        <select id="filter" class="form-select w-auto d-inline" onchange="applyFilter()">
            <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>Tous</option>
            <option value="medicaments_expired" <?= $filter == 'medicaments_expired' ? 'selected' : '' ?>>Expirés</option>
            <option value="medicaments_soon" <?= $filter == 'medicaments_soon' ? 'selected' : '' ?>>Proches de l'expiration</option>
        </select>
    </div>
    
    <div class="row g-3 mt-4 text-center">
        <div class="col-6 col-md-3">
            <div class="card p-3 shadow-sm rounded-lg">
                <i class="fas fa-briefcase-medical fa-3x text-primary"></i>
                <h5 class="mt-3">Sacs Médicaux</h5>
                <p class="display-6 fw-bold"> <?= $total_sacs ?> </p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 shadow-sm rounded-lg">
                <i class="fas fa-pills fa-3x text-secondary"></i>
                <h5 class="mt-3">Médicaments</h5>
                <p class="display-6 fw-bold"> <?= $total_medicaments ?> </p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 shadow-sm rounded-lg">
                <i class="fas fa-boxes fa-3x text-info"></i>
                <h5 class="mt-3">Lots</h5>
                <p class="display-6 fw-bold"> <?= $total_lots ?> </p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 shadow-sm rounded-lg">
                <i class="fas fa-box-open fa-3x text-success"></i>
                <h5 class="mt-3">Consommables</h5>
                <p class="display-6 fw-bold"> <?= $total_consommables ?> </p>
            </div>
        </div>
    </div>

    <h3 class="mt-5">Détails des Médicaments</h3>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
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
