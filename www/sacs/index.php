<?php
include '../includes/db.php';
include '../includes/auth.php';

require '../vendor/autoload.php'; // Inclure l'autoload de Composer

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"> <!-- Animate.css -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"> <!-- AOS -->
    <style>
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
            background-color: rgba(0, 0, 0, 0.8); /* Transparence avec fond noir */
        }

        .navbar-brand img {
            height: 50px;
        }

        .btn {
            border-radius: 30px; /* Boutons arrondis */
            font-weight: bold; /* Texte en gras */
            transition: all 0.3s ease-in-out; /* Animation fluide */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Ombre légère */
        }

        .btn:hover {
            transform: translateY(-3px); /* Effet de levée */
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2); /* Ombre plus forte */
            color: #fff !important; /* Texte blanc au survol */
        }

        .form-inline {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<!-- Inclure le menu -->
<?php include '../menus/menu_sacs.php'; ?>
<div class="container mt-5">
    <h1 class="mb-4">Liste des Sacs Médicaux</h1>

    <!-- Formulaire de recherche et de filtre -->
    <form method="GET" class="form-inline">
        <input type="text" name="search" class="form-control" placeholder="Rechercher par nom ou description" value="<?= htmlspecialchars($search) ?>">
        <select name="lieu_id" class="form-control">
            <option value="">Tous les lieux</option>
            <?php foreach ($lieux as $lieu): ?>
                <option value="<?= $lieu['id'] ?>" <?= $lieu_id == $lieu['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($lieu['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Filtrer</button>
        <a href="?" class="btn btn-secondary">Réinitialiser</a>
    </form>

    <!-- Bouton Ajouter un sac -->
    <a href="add.php" class="btn btn-success mb-3">Ajouter un sac</a>

    <!-- Tableau des sacs -->
    <div class="table-responsive">
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
                                <a href="generate_pdf.php?sac_id=<?= $sac['id'] ?>" class="btn btn-info btn-sm">Télécharger PDF</a>
                                <a href="generate_qrcode.php?sac_id=<?= $sac['id'] ?>" class="btn btn-info btn-sm">Générer QR Code</a>
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
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000, // Durée de l'animation (en ms)
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
</body>
</html>
