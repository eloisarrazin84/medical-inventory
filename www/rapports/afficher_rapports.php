<?php
include '../includes/db.php';
include '../includes/auth.php';
include '../session_manager.php';

// Vérifiez si l'utilisateur est connecté
check_auth();
// Récupérer les valeurs des filtres
$search = $_GET['search'] ?? '';

// Construire la requête avec les filtres
$query = "
    SELECT rapports_utilisation.*, sacs_medicaux.nom AS sac_nom 
    FROM rapports_utilisation
    LEFT JOIN sacs_medicaux ON rapports_utilisation.sac_id = sacs_medicaux.id
    WHERE 1=1
";

$params = [];
if ($search) {
    $query .= " AND (rapports_utilisation.utilisateur LIKE ? OR sacs_medicaux.nom LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY rapports_utilisation.date_saisie DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$rapports = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Rapports d'Utilisation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
<?php include '../menus/menu_usersmanage.php'; ?>
<div class="container mt-5">
    <h1 class="mb-4">Rapports d'Utilisation</h1>

    <!-- Barre de recherche -->
    <form method="GET" class="mb-4">
        <div class="row g-3">
            <div class="col-md-9">
                <input type="text" name="search" class="form-control" placeholder="Rechercher par utilisateur ou nom du sac" value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
        </div>
    </form>

    <!-- Tableau des rapports -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sac</th>
                    <th>Utilisateur</th>
                    <th>Matériel Utilisé</th>
                    <th>Observations</th>
                    <th>Date de Saisie</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rapports)): ?>
                    <?php foreach ($rapports as $rapport): ?>
                        <tr>
                            <td><?= htmlspecialchars($rapport['sac_nom'] ?? 'Non spécifié') ?></td>
                            <td><?= htmlspecialchars($rapport['utilisateur']) ?></td>
                            <td><?= nl2br(htmlspecialchars($rapport['materiels_utilises'])) ?></td>
                            <td><?= nl2br(htmlspecialchars($rapport['observations'])) ?></td>
                            <td><?= htmlspecialchars($rapport['date_saisie']) ?></td>
                            <td>
                                <a href="supprimer_rapport.php?id=<?= $rapport['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rapport ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Aucun rapport trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bouton d'exportation -->
    <a href="exporter_rapports.php" class="btn btn-outline-secondary">Exporter en CSV</a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000 });
</script>
</body>
</html>
