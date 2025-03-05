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

$stmt = $pdo->query("SELECT sacs_medicaux.nom AS sac_nom, medicaments.nom AS med_nom, medicaments.date_expiration FROM medicaments LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id WHERE medicaments.date_expiration < CURDATE() ORDER BY sacs_medicaux.nom");
$expired_medicaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Regrouper les médicaments par sac et compter les expirés
$grouped_medicaments = [];
$now = new DateTime();
foreach ($expired_medicaments as $med) {
    $exp_date = new DateTime($med['date_expiration']);
    $diff = $now->diff($exp_date)->days;
    $severity = $diff > 60 ? '🔴' : ($diff > 30 ? '🟠' : '🟢');
    $med['severity'] = $severity;
    $grouped_medicaments[$med['sac_nom']][] = $med;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Tableau de Bord</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .toggle-btn { cursor: pointer; font-size: 18px; font-weight: bold; color: #007bff; display: flex; align-items: center; justify-content: space-between; background: #e9ecef; padding: 12px; border-radius: 10px; margin-top: 10px; transition: background 0.3s ease; }
        .toggle-btn:hover { background: #d6d8db; }
        .table-container { display: none; transition: all 0.3s ease-in-out; }
        .table th { background: #dc3545; color: white; }
        .table-hover tbody tr:hover { background-color: rgba(220, 53, 69, 0.1); }
        .toggle-icon { transition: transform 0.3s ease; }
        .expanded .toggle-icon { transform: rotate(180deg); }
    </style>
    <script>
        function toggleTable(id) {
            let table = document.getElementById(id);
            let toggleButton = document.getElementById('toggle-' + id);
            if (table.style.display === "none" || table.style.display === "") {
                table.style.display = "block";
                toggleButton.classList.add("expanded");
            } else {
                table.style.display = "none";
                toggleButton.classList.remove("expanded");
            }
        }
        function toggleAll(expand) {
            document.querySelectorAll('.table-container').forEach(table => table.style.display = expand ? "block" : "none");
            document.querySelectorAll('.toggle-btn').forEach(btn => btn.classList.toggle("expanded", expand));
        }
    </script>
</head>
<body>
<?php include 'menus/menu_dashboard.php'; ?>
<div class="container mt-5">
    <h1 class="text-center mb-4">Tableau de Bord</h1>
    <div class="text-end mb-3">
        <a href="export_pdf.php" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Exporter en PDF</a>
        <a href="export_excel.php" class="btn btn-success"><i class="fas fa-file-excel"></i> Exporter en Excel</a>
        <button class="btn btn-primary" onclick="toggleAll(true)">Tout Déplier</button>
        <button class="btn btn-secondary" onclick="toggleAll(false)">Tout Replier</button>
    </div>
    <h3 class="mt-5 text-danger">Médicaments Expirés par Sac</h3>
    <div class="table-responsive">
        <?php foreach ($grouped_medicaments as $sac_nom => $medicaments): ?>
            <div id="toggle-table-<?= md5($sac_nom) ?>" class="toggle-btn" onclick="toggleTable('table-<?= md5($sac_nom) ?>')">
                <span><i class="fas fa-box-medical me-2"></i> Sac: <?= htmlspecialchars($sac_nom) ?> <span class="badge bg-danger ms-2"> <?= count($medicaments) ?> </span></span>
                <i class="fas fa-chevron-down toggle-icon" id="icon-table-<?= md5($sac_nom) ?>"></i>
            </div>
            <div id="table-<?= md5($sac_nom) ?>" class="table-container">
                <table class="table table-hover mt-2">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Date d'Expiration</th>
                            <th>Gravité</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medicaments as $med): ?>
                            <tr>
                                <td><?= htmlspecialchars($med['med_nom']) ?></td>
                                <td><?= htmlspecialchars($med['date_expiration']) ?></td>
                                <td><?= $med['severity'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
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
