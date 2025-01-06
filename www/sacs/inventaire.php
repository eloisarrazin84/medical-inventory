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

// Récupérer les médicaments associés au sac
$stmt = $pdo->prepare("SELECT * FROM medicaments WHERE sac_id = ?");
$stmt->execute([$sac_id]);
$medicaments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Inventaire - <?= htmlspecialchars($sac['nom']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 20px;
            background-color: #f9f9f9;
        }

        header {
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 1030;
            border-bottom: 1px solid #ddd;
            padding: 10px 15px;
        }

        .btn-group {
            margin: 10px 0;
        }

        .card {
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            font-weight: bold;
            font-size: 1.2em;
            background-color: #007bff;
            color: white;
            padding: 10px;
        }

        .badge-warning {
            background-color: #ffc107;
            color: black !important; /* Texte noir pour contraste */
        }

        .badge-danger {
            background-color: #dc3545;
            color: white; /* Texte blanc sur rouge */
        }

        .badge-success {
            background-color: #28a745;
            color: white; /* Texte blanc sur vert */
        }

        @media (max-width: 768px) {
            .btn {
                width: 100%; /* Boutons plein écran pour mobile */
            }

            .card-header {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
<header class="d-flex justify-content-between align-items-center">
    <h1 class="h5 mb-0">Inventaire : <?= htmlspecialchars($sac['nom']) ?></h1>
    <div>
        <a href="../incidents/signaler_incident.php?sac_id=<?= $sac['id'] ?>" class="btn btn-danger btn-sm">Signaler un Incident</a>
    </div>
</header>
<div class="container mt-3">
    <?php if (!empty($medicaments)): ?>
        <div class="row">
            <?php foreach ($medicaments as $med): ?>
                <div class="col-md-6 col-lg-4">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
