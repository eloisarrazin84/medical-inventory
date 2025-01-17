<?php
include '../includes/db.php';
include '../includes/auth.php';
include '../session_manager.php';

// Vérifiez si l'utilisateur est connecté
check_auth();
// Récupérer les valeurs des filtres
$search = $_GET['search'] ?? '';
$statut = $_GET['statut'] ?? '';
$type_incident = $_GET['type_incident'] ?? '';

// Construire la requête avec les filtres
$query = "
    SELECT incidents.*, sacs_medicaux.nom AS sac_nom 
    FROM incidents 
    LEFT JOIN sacs_medicaux ON incidents.reference_id = sacs_medicaux.id 
    WHERE 1=1
";

$params = [];
if ($search) {
    $query .= " AND (description LIKE ? OR sacs_medicaux.nom LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($statut) {
    $query .= " AND statut = ?";
    $params[] = $statut;
}
if ($type_incident) {
    $query .= " AND type_incident = ?";
    $params[] = $type_incident;
}

$query .= " ORDER BY incidents.date_signalement DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$incidents = $stmt->fetchAll();

// Fonction pour le style des badges
function getBadgeClass($statut) {
    switch ($statut) {
        case 'Non Résolu':
            return 'badge-danger';
        case 'En Cours':
            return 'badge-warning';
        case 'Résolu':
            return 'badge-success';
        default:
            return 'badge-secondary';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Gestion des Incidents</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
    <!-- Inclure le menu -->
<?php include '../menus/menu_usersmanage.php'; ?>
<div class="container mt-5">
    <h1 class="mb-4">Suivi des Incidents</h1>

    <!-- Barre de recherche -->
    <form method="GET" class="mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Rechercher par description ou nom du sac" value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <select name="statut" class="form-control">
                    <option value="">Tous les statuts</option>
                    <option value="Non Résolu" <?= $statut === 'Non Résolu' ? 'selected' : '' ?>>Non Résolu</option>
                    <option value="En Cours" <?= $statut === 'En Cours' ? 'selected' : '' ?>>En Cours</option>
                    <option value="Résolu" <?= $statut === 'Résolu' ? 'selected' : '' ?>>Résolu</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="type_incident" class="form-control">
                    <option value="">Tous les types</option>
                    <option value="Sac" <?= $type_incident === 'Sac' ? 'selected' : '' ?>>Sac</option>
                    <option value="Médicament" <?= $type_incident === 'Médicament' ? 'selected' : '' ?>>Médicament</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
        </div>
    </form>

    <!-- Tableau des incidents -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Référence (Nom du Sac)</th>
                    <th>Description</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($incidents)): ?>
                    <?php foreach ($incidents as $incident): ?>
                        <tr>
                            <td><?= htmlspecialchars($incident['type_incident']) ?></td>
                            <td><?= htmlspecialchars($incident['sac_nom'] ?? 'Non spécifié') ?></td>
                            <td><?= htmlspecialchars($incident['description']) ?></td>
                            <td>
                                <span class="badge <?= getBadgeClass($incident['statut']) ?>">
                                    <?= htmlspecialchars($incident['statut']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($incident['date_signalement']) ?></td>
                            <td>
                                <a href="changer_statut.php?id=<?= $incident['id'] ?>&statut=En Cours" class="btn btn-warning btn-sm">En Cours</a>
                                <a href="changer_statut.php?id=<?= $incident['id'] ?>&statut=Résolu" class="btn btn-success btn-sm">Résolu</a>
                            </td>
                            <td>
                                <a href="supprimer_incident.php?id=<?= $incident['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet incident ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Aucun incident trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bouton d'exportation -->
    <a href="exporter_incidents.php" class="btn btn-outline-secondary">Exporter en CSV</a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000 });
</script>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
</body>
</html>
