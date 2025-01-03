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
        }

        .card {
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            font-weight: bold;
        }

        .btn {
            border-radius: 20px;
        }

        .btn-group {
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .btn {
                width: 100%; /* Les boutons prennent toute la largeur sur mobile */
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="text-center mb-4">Inventaire : <?= htmlspecialchars($sac['nom']) ?></h1>
    
    <!-- Boutons d'actions -->
    <div class="btn-group d-flex justify-content-between">
        <a href="../dashboard.php" class="btn btn-secondary">Retour</a>
        <a href="../incidents/signaler_incident.php?sac_id=<?= $sac['id'] ?>" class="btn btn-danger">Signaler un Incident</a>
    </div>

    <!-- Liste des médicaments -->
    <?php if (!empty($medicaments)): ?>
        <div class="row mt-4">
            <?php foreach ($medicaments as $med): ?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <?= htmlspecialchars($med['nom']) ?>
                        </div>
                        <div class="card-body">
                            <p><strong>Description :</strong> <?= htmlspecialchars($med['description']) ?: 'Non spécifiée' ?></p>
                            <p><strong>Quantité :</strong> <?= htmlspecialchars($med['quantite']) ?></p>
                            <p><strong>Date d'expiration :</strong> <?= htmlspecialchars($med['date_expiration']) ?: 'Non spécifiée' ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="alert alert-warning text-center mt-4">Aucun médicament trouvé pour ce sac.</p>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
