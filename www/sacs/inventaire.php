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

// Récupérer les filtres de recherche pour les médicaments
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

// Récupérer les lots associés au sac
$stmt = $pdo->prepare("SELECT * FROM lots WHERE sac_id = ?");
$stmt->execute([$sac_id]);
$lots = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Inventaire - <?= htmlspecialchars($sac['nom']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"> <!-- Animate.css -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"> <!-- AOS -->
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

        .accordion-button:not(.collapsed) {
            background-color: #007bff;
            color: #fff;
        }

        .accordion-body {
            padding: 15px;
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
        flex-direction: column;
        gap: 10px;
        z-index: 1050;
    }

    .floating-buttons .btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem; /* Taille de l'icône */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        text-decoration: none;
    }

    .floating-buttons .btn:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.3);
    }

    .btn-rapport {
        background-color: #28a745; /* Vert pour les rapports */
        color: white;
    }

    .btn-rapport:hover {
        background-color: #218838;
    }

    .btn-incident {
        background-color: #dc3545; /* Rouge pour les incidents */
        color: white;
    }

    .btn-incident:hover {
        background-color: #c82333;
    }

    .btn i {
        margin-right: 0; /* Supprime l'espacement entre l'icône et le texte */
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
    <!-- Barre de recherche et de filtre pour les médicaments -->
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

  <div class="container mt-3">
    <!-- Accordéon pour les Médicaments -->
    <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingMedicaments">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMedicaments" aria-expanded="true" aria-controls="collapseMedicaments">
                    Médicaments
                </button>
            </h2>
            <div id="collapseMedicaments" class="accordion-collapse collapse show" aria-labelledby="headingMedicaments" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <?php if (!empty($medicaments)): ?>
                        <div class="row">
                            <?php foreach ($medicaments as $med): ?>
                                <div class="col-12">
                                    <div class="card mb-2">
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
                                            <p><strong>Date d'expiration :</strong> <?= htmlspecialchars($med['date_expiration']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="alert alert-warning text-center">Aucun médicament trouvé pour ce sac.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Accordéon pour les Lots et Consommables -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingLots">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLots" aria-expanded="false" aria-controls="collapseLots">
                    Lots et Consommables
                </button>
            </h2>
            <div id="collapseLots" class="accordion-collapse collapse" aria-labelledby="headingLots" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <?php if (!empty($lots)): ?>
                        <?php foreach ($lots as $lot): ?>
                            <div class="card mb-3">
                                <div class="card-header"><?= htmlspecialchars($lot['nom']) ?></div>
                                <div class="card-body">
                                    <p><?= htmlspecialchars($lot['description']) ?: 'Aucune description' ?></p>
                                    <h5>Consommables</h5>
                                    <?php
                                    $stmt = $pdo->prepare("SELECT * FROM consommables WHERE lot_id = ?");
                                    $stmt->execute([$lot['id']]);
                                    $consommables = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    ?>
                                    <?php if (!empty($consommables)): ?>
                                        <ul>
                                            <?php foreach ($consommables as $cons): ?>
                                                <li>
                                                    <?= htmlspecialchars($cons['nom']) ?>
                                                    - Quantité : <?= $cons['quantite'] ?>
                                                    - Expire le : <?= htmlspecialchars($cons['date_expiration']) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted">Aucun consommable ajouté.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="alert alert-warning text-center">Aucun lot trouvé pour ce sac.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Boutons flottants -->
<div class="floating-buttons">
    <a href="../rapports/creer_rapport.php?sac_id=<?= $sac['id'] ?>" title="Créer un rapport" class="btn btn-rapport">
        <i class="fas fa-file-alt"></i>
    </a>
    <a href="../incidents/signaler_incident.php?sac_id=<?= $sac['id'] ?>" title="Signaler un incident" class="btn btn-incident">
        <i class="fas fa-exclamation-triangle"></i>
    </a>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
