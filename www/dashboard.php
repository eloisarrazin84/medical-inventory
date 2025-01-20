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

$stmt = $pdo->query("
    SELECT medicaments.nom AS med_nom, medicaments.date_expiration, sacs_medicaux.nom AS sac_nom
    FROM medicaments
    LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id
    WHERE medicaments.date_expiration < CURDATE()
");
$details_medicaments_expires = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
    SELECT medicaments.nom AS med_nom, medicaments.date_expiration, sacs_medicaux.nom AS sac_nom
    FROM medicaments
    LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id
    WHERE medicaments.date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
");
$details_medicaments_proches_expiration = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
    SELECT consommables.nom AS cons_nom, consommables.date_expiration, lots.nom AS lot_nom, sacs_medicaux.nom AS sac_nom
    FROM consommables
    LEFT JOIN lots ON consommables.lot_id = lots.id
    LEFT JOIN sacs_medicaux ON lots.sac_id = sacs_medicaux.id
    WHERE consommables.date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
");
$details_consommables_proches_expiration = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
    SELECT consommables.nom AS cons_nom, consommables.date_expiration, lots.nom AS lot_nom, sacs_medicaux.nom AS sac_nom
    FROM consommables
    LEFT JOIN lots ON consommables.lot_id = lots.id
    LEFT JOIN sacs_medicaux ON lots.sac_id = sacs_medicaux.id
    WHERE consommables.date_expiration < CURDATE()
");
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
<?php include 'menus/menu_dashboard.php'; ?>
<div class="page-content">
<div class="container mt-5">
    <h1 class="text-center mb-4">Tableau de Bord</h1>
    <div class="row text-center g-3">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-primary" onclick="toggleDetails('medicamentsExpires')">
                <i class="fas fa-times-circle card-icon"></i>
                <h5>Médicaments Expirés</h5>
                <p><?= count($details_medicaments_expires) ?></p>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-warning" onclick="toggleDetails('prochesMedicaments')">
                <i class="fas fa-exclamation-circle card-icon"></i>
                <h5>Médicaments Proches Expiration</h5>
                <p><?= count($details_medicaments_proches_expiration) ?></p>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-danger" onclick="toggleDetails('consommablesExpires')">
                <i class="fas fa-trash-alt card-icon"></i>
                <h5>Consommables Expirés</h5>
                <p><?= count($details_consommables_expires) ?></p>
            </div>
        </div>

        <!-- Nouvelle carte : Consommables Proches Expiration -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card bg-warning" onclick="toggleDetails('prochesConsommables')">
                <i class="fas fa-clock card-icon"></i>
                <h5>Consommables Proches Expiration</h5>
                <p><?= count($details_consommables_proches_expiration) ?></p>
            </div>
        </div>
    </div>

    <!-- Médicaments Expirés -->
    <div id="medicamentsExpires" class="details">
        <h3>Médicaments Expirés</h3>
        <table class="table">
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

    <!-- Médicaments Proches Expiration -->
    <div id="prochesMedicaments" class="details">
        <h3>Médicaments Proches de l'Expiration</h3>
        <table class="table">
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

    <!-- Consommables Expirés -->
    <div id="consommablesExpires" class="details">
        <h3>Consommables Expirés</h3>
        <table class="table">
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

    <!-- Consommables Proches Expiration -->
    <div id="prochesConsommables" class="details">
        <h3>Consommables Proches de l'Expiration</h3>
        <table class="table">
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
</div>
<!-- Bouton de bascule du thème -->
<button class="theme-toggle-btn" onclick="toggleTheme()" title="Changer de thème">
    <i class="fas fa-adjust"></i>
</button>
<script>
    function toggleDetails(id) {
        const sections = document.querySelectorAll('.details');
        sections.forEach(section => section.style.display = 'none');
        document.getElementById(id).style.display = 'block';
    }
      // Fonction pour basculer entre les thèmes
    function toggleTheme() {
        document.body.classList.toggle("dark-mode");
        const isDarkMode = document.body.classList.contains("dark-mode");
        localStorage.setItem("theme", isDarkMode ? "dark" : "light");
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
