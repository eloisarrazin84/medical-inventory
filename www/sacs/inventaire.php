<?php
include '../includes/db.php';

// Vérifier si l'ID du sac est fourni
$sac_id = $_GET['sac_id'] ?? null;

if (!$sac_id) {
    die('Erreur : Aucun sac sélectionné.');
}

// Vérifier si le sac existe dans la base de données
$stmt = $pdo->prepare("SELECT * FROM sacs_medicaux WHERE id = ?");
$stmt->execute([$sac_id]);
$sac = $stmt->fetch();

if (!$sac) {
    die('Erreur : Sac médical introuvable.');
}

// Récupérer les filtres de recherche
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';
$type_produit = $_GET['type_produit'] ?? '';

// Construire la requête des médicaments associés au sac avec les filtres
$query = "SELECT * FROM medicaments WHERE sac_id = ?";
$params = [$sac_id];

if (!empty($search)) {
    $query .= " AND (nom LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filter === 'expired') {
    $query .= " AND date_expiration < CURDATE()";
} elseif ($filter === 'low_stock') {
    $query .= " AND quantite < 5";
}

if (!empty($type_produit)) {
    $query .= " AND type_produit = ?";
    $params[] = $type_produit;
}

$query .= " ORDER BY nom ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$medicaments = $stmt->fetchAll();

// Récupérer les types de médicaments pour le filtre
$stmt = $pdo->prepare("SELECT DISTINCT type_produit FROM medicaments WHERE sac_id = ?");
$stmt->execute([$sac_id]);
$types_produit = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Inventaire - <?= htmlspecialchars($sac['nom']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        header {
            position: sticky;
            top: 0;
            z-index: 1030;
            background-color: #007bff;
            color: #fff;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 1.2rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .search-bar {
            padding: 10px 15px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .btn {
            border-radius: 30px;
            font-weight: bold;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .card {
            margin-bottom: 15px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            font-weight: bold;
            font-size: 1.1rem;
            border-radius: 15px 15px 0 0;
        }

        .badge {
            font-size: 0.9rem;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }

        .badge-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .badge-success {
            background-color: #28a745;
            color: #fff;
        }

        .floating-buttons {
            position: fixed;
            bottom: 15px;
            right: 15px;
            display: flex;
            gap: 10px;
            z-index: 1050;
        }

        .floating-buttons a {
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        .floating-buttons a:hover {
            background-color: #0056b3;
        }

        @media (max-width: 768px) {
            .form-select, .form-control {
                font-size: 0.9rem;
            }

            .card-header {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

<header>
    Inventaire : <?= htmlspecialchars($sac['nom']) ?>
</header>

<div class="container mt-3">
    <!-- Barre de recherche et de filtre -->
    <form method="GET" class="search-bar">
        <input type="hidden" name="sac_id" value="<?= htmlspecialchars($sac_id) ?>">
        <div class="row g-2">
            <div class="col-12">
                <input type="text" name="search" class="form-control" placeholder="Rechercher un médicament" value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-6">
                <select name="filter" class="form-select">
                    <option value="">Tous les médicaments</option>
                    <option value="expired" <?= $filter === 'expired' ? 'selected' : '' ?>>Médicaments expirés</option>
                    <option value="low_stock" <?= $filter === 'low_stock' ? 'selected' : '' ?>>Stock faible</option>
                </select>
            </div>
            <div class="col-6">
                <select name="type_produit" class="form-select">
                    <option value="">Tous les types</option>
                    <?php foreach ($types_produit as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>" <?= $type_produit === $type ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary w-100">Appliquer</button>
            </div>
        </div>
    </form>

    <!-- Liste des médicaments -->
    <?php if (!empty($medicaments)): ?>
        <div class="row">
            <?php foreach ($medicaments as $med): ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <?= htmlspecialchars($med['nom']) ?>
                        </div>
                        <div class="card-body">
                            <p><strong>Description :</strong> <?= htmlspecialchars($med['description']) ?: 'Non spécifiée' ?></p>
                            <p><strong>Quantité :</strong> 
                                <span class="badge <?= $med['quantite'] < 5 ? 'badge-warning' : 'badge-success' ?>">
                                    <?= htmlspecialchars($med['quantite']) ?>
                                </span>
                            </p>
                            <p><strong>Type :</strong> <?= htmlspecialchars($med['type_produit']) ?: 'Non spécifié' ?></p>
                            <p><strong>Numéro de lot :</strong> <?= htmlspecialchars($med['numero_lot']) ?: 'Non spécifié' ?></p>
                            <p><strong>Date d'expiration :</strong> 
                                <?php if (!empty($med['date_expiration']) && $med['date_expiration'] !== '0000-00-00'): ?>
                                    <span class="badge <?= strtotime($med['date_expiration']) < time() ? 'badge-danger' : 'badge-success' ?>">
                                        <?= htmlspecialchars($med['date_expiration']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Non spécifiée</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="alert alert-warning text-center">Aucun médicament trouvé pour ce sac.</p>
    <?php endif; ?>
</div>

<!-- Boutons flottants -->
<div class="floating-buttons">
    <a href="../incidents/signaler_incident.php?sac_id=<?= $sac['id'] ?>" title="Signaler un incident">
        <i class="fas fa-exclamation-circle"></i>
    </a>
    <a href="../rapports/creer_rapport.php?sac_id=<?= $sac['id'] ?>" title="Créer un rapport">
        <i class="fas fa-file-alt"></i>
    </a>
</div>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
