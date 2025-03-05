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

$stmt = $pdo->query("SELECT medicaments.nom AS med_nom, medicaments.date_expiration, sacs_medicaux.nom AS sac_nom FROM medicaments LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id WHERE medicaments.date_expiration < CURDATE()");
$details_medicaments_expires = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT medicaments.nom AS med_nom, medicaments.date_expiration, sacs_medicaux.nom AS sac_nom FROM medicaments LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id WHERE medicaments.date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
$details_medicaments_proches_expiration = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT consommables.nom AS cons_nom, consommables.date_expiration, lots.nom AS lot_nom, sacs_medicaux.nom AS sac_nom FROM consommables LEFT JOIN lots ON consommables.lot_id = lots.id LEFT JOIN sacs_medicaux ON lots.sac_id = sacs_medicaux.id WHERE consommables.date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
$details_consommables_proches_expiration = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT consommables.nom AS cons_nom, consommables.date_expiration, lots.nom AS lot_nom, sacs_medicaux.nom AS sac_nom FROM consommables LEFT JOIN lots ON consommables.lot_id = lots.id LEFT JOIN sacs_medicaux ON lots.sac_id = sacs_medicaux.id WHERE consommables.date_expiration < CURDATE()");
$details_consommables_expires = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
<?php include 'menus/menu_usersmanage.php'; ?>
<div class="container mt-5">
    <h1 class="text-center mb-4">Tableau de Bord</h1>
    <div class="row text-center g-3">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-primary">
                <i class="fas fa-briefcase-medical card-icon"></i>
                <h5>Total des Sacs Médicaux</h5>
                <p><?= $total_sacs ?></p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-secondary">
                <i class="fas fa-pills card-icon"></i>
                <h5>Total des Médicaments</h5>
                <p><?= $total_medicaments ?></p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-info">
                <i class="fas fa-boxes card-icon"></i>
                <h5>Total des Lots</h5>
                <p><?= $total_lots ?></p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-success">
                <i class="fas fa-box-open card-icon"></i>
                <h5>Total des Consommables</h5>
                <p><?= $total_consommables ?></p>
            </div>
        </div>
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
