<?php
include '../includes/db.php';
include '../includes/auth.php';

require '../vendor/autoload.php'; // Inclure l'autoload de Composer
include '../session_manager.php';

// Vérifiez si l'utilisateur est connecté
check_auth();
// Récupérer les filtres
$search = $_GET['search'] ?? '';
$lieu_id = $_GET['lieu_id'] ?? '';

// Construire la requête avec les filtres
$query = "
    SELECT sacs_medicaux.*, lieux_stockage.nom AS lieu_nom
    FROM sacs_medicaux
    LEFT JOIN lieux_stockage ON sacs_medicaux.lieu_id = lieux_stockage.id
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $query .= " AND (sacs_medicaux.nom LIKE ? OR sacs_medicaux.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($lieu_id)) {
    $query .= " AND lieux_stockage.id = ?";
    $params[] = $lieu_id;
}

$query .= " ORDER BY date_creation DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$sacs = $stmt->fetchAll();

// Récupérer la liste des lieux de stockage pour le filtre
$stmt = $pdo->query("SELECT id, nom FROM lieux_stockage ORDER BY nom ASC");
$lieux = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Liste des Sacs Médicaux</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"> <!-- Animate.css -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"> <!-- AOS -->
    <link href="../css/styles.css" rel="stylesheet">
    <style>
    <style>
        /* Optimisation pour Mobile */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }
            .page-header .btn {
                width: 100%;
            }
            .table-responsive {
                overflow-x: auto;
            }
            .btn-group {
                display: block;
                margin-top: 5px;
            }
            .btn-sm {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
            .dropdown-menu {
                width: 100%;
            }
        }
        .page-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-bar {
            flex: 1;
            min-width: 250px;
        }

        .btn-group {
            margin-left: 10px;
        }
    </style>
</head>
<body>
<!-- Inclure le menu -->
<?php include '../menus/menu_usersmanage.php'; ?>
<div class="container mt-4">
    <h1 class="mb-3 text-center">Liste des Sacs Médicaux</h1>

    <!-- Formulaire de recherche et filtres -->
    <form method="GET" class="page-header d-flex flex-wrap align-items-center gap-2">
        <input type="text" name="search" class="form-control flex-grow-1" placeholder="Rechercher par nom ou description" value="<?= htmlspecialchars($search) ?>">
        <select name="lieu_id" class="form-control flex-grow-1">
            <option value="">Tous les lieux</option>
            <?php foreach ($lieux as $lieu): ?>
                <option value="<?= $lieu['id'] ?>" <?= $lieu_id == $lieu['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($lieu['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Filtrer</button>
        <a href="?" class="btn btn-secondary">Réinitialiser</a>
        <a href="add.php" class="btn btn-success">Ajouter un sac</a>
    </form>

    <!-- Tableau des sacs -->
    <div class="table-responsive mt-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Date de création</th>
                    <th>Lieu de Stockage</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($sacs)): ?>
                    <?php foreach ($sacs as $sac): ?>
                        <tr>
                            <td><?= htmlspecialchars($sac['nom']) ?></td>
                            <td><?= htmlspecialchars($sac['description']) ?></td>
                            <td><?= htmlspecialchars($sac['date_creation']) ?></td>
                            <td><?= htmlspecialchars($sac['lieu_nom'] ?? 'Non associé') ?></td>
                            <td>
                                <a href="edit.php?id=<?= $sac['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="delete.php?id=<?= $sac['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer ce sac ?')">Supprimer</a>
                                <a href="../lieux/associer_lieu.php?sac_id=<?= $sac['id'] ?>" class="btn btn-secondary btn-sm">Associer un lieu</a>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                        Générer Documents
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="generate_pdf.php?sac_id=<?= $sac['id'] ?>"><i class="fas fa-file-pdf"></i> Télécharger inventaire PDF</a></li>
                                        <li><a class="dropdown-item" href="generate_order.php?sac_id=<?= $sac['id'] ?>"><i class="fas fa-shopping-cart"></i> Générer Fiche de Commande</a></li>
                                        <li><a class="dropdown-item" href="generate_qrcode.php?sac_id=<?= $sac['id'] ?>"><i class="fas fa-qrcode"></i> Générer QR Code</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Aucun sac trouvé.</td>
                    </tr>
                <?php endif; ?>
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
