<?php
include 'includes/db.php';
include 'includes/auth.php';
include 'session_manager.php';

// Vérifiez si l'utilisateur est connecté
check_auth();

// Requêtes pour les données du tableau de bord
$stmt = $pdo->query("SELECT COUNT(*) AS total_sacs FROM sacs_medicaux");
$total_sacs = $stmt->fetch()['total_sacs'];

$stmt = $pdo->query("SELECT COUNT(*) AS total_medicaments FROM medicaments");
$total_medicaments = $stmt->fetch()['total_medicaments'];

$stmt = $pdo->query("SELECT COUNT(*) AS total_lots FROM lots");
$total_lots = $stmt->fetch()['total_lots'];

$stmt = $pdo->query("SELECT COUNT(*) AS total_consommables FROM consommables");
$total_consommables = $stmt->fetch()['total_consommables'];

$stmt = $pdo->query("SELECT COUNT(*) AS total_medicaments_expires FROM medicaments WHERE date_expiration < CURDATE()");
$total_medicaments_expires = $stmt->fetch()['total_medicaments_expires'];

$stmt = $pdo->query("SELECT medicaments.nom AS med_nom, medicaments.date_expiration, sacs_medicaux.nom AS sac_nom FROM medicaments LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id WHERE medicaments.date_expiration < CURDATE()");
$expired_medicaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        .card-summary {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease-in-out;
            position: relative;
        }
        .card-summary:hover {
            transform: translateY(-5px);
        }
        .card-summary i {
            font-size: 40px;
        }
        .badge-alert {
            position: absolute;
            top: 10px;
            right: 10px;
            background: red;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }
        .table th {
            background: #dc3545;
            color: white;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(220, 53, 69, 0.1);
        }
    </style>
</head>
<body>
<?php include 'menus/menu_dashboard.php'; ?>
<div class="container mt-5">
    <h1 class="text-center mb-4">Tableau de Bord</h1>
    
    <div class="row g-3 mt-4 text-center">
        <div class="col-6 col-md-3">
            <div class="card-summary">
                <i class="fas fa-briefcase-medical"></i>
                <h5 class="mt-2">Sacs Médicaux</h5>
                <p class="display-6 fw-bold"><?= $total_sacs ?></p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-summary" style="background: linear-gradient(135deg, #6c757d, #343a40);">
                <i class="fas fa-pills"></i>
                <h5 class="mt-2">Médicaments</h5>
                <p class="display-6 fw-bold"><?= $total_medicaments ?></p>
                <?php if ($total_medicaments_expires > 0): ?>
                    <span class="badge-alert">⚠ <?= $total_medicaments_expires ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-summary" style="background: linear-gradient(135deg, #17a2b8, #117a8b);">
                <i class="fas fa-boxes"></i>
                <h5 class="mt-2">Lots</h5>
                <p class="display-6 fw-bold"><?= $total_lots ?></p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-summary" style="background: linear-gradient(135deg, #28a745, #1e7e34);">
                <i class="fas fa-box-open"></i>
                <h5 class="mt-2">Consommables</h5>
                <p class="display-6 fw-bold"><?= $total_consommables ?></p>
            </div>
        </div>
    </div>

    <h3 class="mt-5 text-danger">Médicaments Expirés</h3>
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
                <?php foreach ($expired_medicaments as $med): ?>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
    });
</script>    
</body>
</html>
