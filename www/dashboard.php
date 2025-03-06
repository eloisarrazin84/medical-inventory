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

$stmt = $pdo->query("SELECT COUNT(*) AS total_medicaments_expires FROM medicaments WHERE date_expiration < CURDATE()");
$total_medicaments_expires = $stmt->fetch()['total_medicaments_expires'];

$stmt = $pdo->query("SELECT sacs_medicaux.nom AS sac_nom, medicaments.nom AS med_nom, medicaments.date_expiration 
                     FROM medicaments 
                     LEFT JOIN sacs_medicaux ON medicaments.sac_id = sacs_medicaux.id 
                     WHERE medicaments.date_expiration < CURDATE() 
                     ORDER BY sacs_medicaux.nom");
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
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; transition: background 0.3s ease; }
        .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .toggle-btn { cursor: pointer; font-size: 18px; font-weight: bold; color: #007bff; display: flex; align-items: center; justify-content: space-between; background: #e9ecef; padding: 12px; border-radius: 10px; margin-top: 10px; transition: background 0.3s ease; }
        .toggle-btn:hover { background: #d6d8db; }
        .table-container { display: none; transition: all 0.3s ease-in-out; }
        .table th { background: #dc3545; color: white; }
        .table-hover tbody tr:hover { background-color: rgba(220, 53, 69, 0.1); }
        .toggle-icon { transition: transform 0.3s ease; }
        .expanded .toggle-icon { transform: rotate(180deg); }

        /* Mode sombre */
        .dark-mode { background-color: #343a40 !important; color: white; }
        .dark-mode .container { background: #454d55; color: white; }
        .dark-mode .toggle-btn { background: #5a6268; color: white; }
        .dark-mode .toggle-btn:hover { background: #6c757d; }
        .dark-mode .table th { background: #dc3545; color: white; }

        /* Notifications */
        .notifications-container { position: fixed; top: 20px; right: 20px; z-index: 1050; width: 300px; }
        .notification { background: #dc3545; color: white; padding: 10px 20px; border-radius: 5px; margin-bottom: 10px; display: none; }
    </style>
</head>
<body>

<!-- Notifications -->
<div class="notifications-container">
    <div id="notifications"></div>
</div>

<?php include 'menus/menu_dashboard.php'; ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Tableau de Bord</h1>
    <div class="text-end mb-3">
        <button class="btn btn-dark" onclick="toggleDarkMode()"><i class="fas fa-moon"></i> Mode Sombre</button>
        <a href="export_pdf.php" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Exporter en PDF</a>
        <a href="export_excel.php" class="btn btn-success"><i class="fas fa-file-excel"></i> Exporter en Excel</a>
    </div>
    
    <h3 class="mt-5 text-danger">Médicaments Expirés par Sac</h3>
    <div class="table-responsive">
        <?php foreach ($grouped_medicaments as $sac_nom => $medicaments): ?>
            <div class="toggle-btn" onclick="toggleTable('table-<?= md5($sac_nom) ?>')">
                <span><i class="fas fa-box-medical me-2"></i> Sac: <?= htmlspecialchars($sac_nom) ?> <span class="badge bg-danger ms-2"> <?= count($medicaments) ?> </span></span>
                <i id="icon-table-<?= md5($sac_nom) ?>" class="fas fa-chevron-down toggle-icon"></i>
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

<script>
function toggleTable(id) {
    let table = document.getElementById(id);
    let icon = document.getElementById('icon-' + id);
    table.style.display = (table.style.display === "none" || table.style.display === "") ? "block" : "none";
    icon.classList.toggle("expanded");
}

function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
    localStorage.setItem("darkMode", document.body.classList.contains("dark-mode") ? "enabled" : "disabled");
}

function loadNotifications() {
    fetch('notifications/notifications.php')  // Charge les notifications
        .then(response => response.json())
        .then(data => {
            const notifContainer = document.getElementById("notif-list"); // ID du menu
            notifContainer.innerHTML = "";

            if (data.length > 0) {
                data.forEach(notif => {
                    const notifItem = document.createElement("a");
                    notifItem.href = "#";  // Ou une page de détails
                    notifItem.className = "dropdown-item";
                    notifItem.innerHTML = `
                        <span class="badge bg-${notif.type === 'danger' ? 'danger' : (notif.type === 'warning' ? 'warning' : 'info')}">●</span>
                        ${notif.message}
                    `;
                    notifContainer.appendChild(notifItem);
                });

                document.getElementById("notif-badge").innerText = data.length; // Affiche le nombre de notifications
            } else {
                notifContainer.innerHTML = "<a class='dropdown-item'>Aucune nouvelle notification</a>";
                document.getElementById("notif-badge").innerText = ""; // Cache le badge si 0 notifications
            }
        })
        .catch(error => console.error("Erreur de chargement des notifications :", error));
}

// Charge les notifications toutes les 5 secondes
setInterval(loadNotifications, 5000);
window.onload = loadNotifications;
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
