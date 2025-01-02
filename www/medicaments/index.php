<?php
include '../includes/db.php';
include 'includes/auth.php';

// Vérifier si `sac_id` est défini et valide dans l'URL
if (!isset($_GET['sac_id']) || empty($_GET['sac_id'])) {
    die('Erreur : ID du sac non défini ou invalide.');
}

$sac_id = $_GET['sac_id'];

// Récupérer les informations du sac
$stmt = $pdo->prepare("SELECT * FROM sacs_medicaux WHERE id = ?");
$stmt->execute([$sac_id]);
$sac = $stmt->fetch();

if (!$sac) {
    die('Erreur : Sac médical non trouvé.');
}

// Récupérer les médicaments associés au sac
$stmt = $pdo->prepare("SELECT * FROM medicaments WHERE sac_id = ?");
$stmt->execute([$sac_id]);
$medicaments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Médicaments - <?= htmlspecialchars($sac['nom'] ?? 'Nom inconnu') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Médicaments - <?= htmlspecialchars($sac['nom'] ?? 'Nom inconnu') ?></h1>
    <a href="../sacs/index.php" class="btn btn-secondary mb-3">Retour aux sacs</a>
    <a href="add.php?sac_id=<?= htmlspecialchars($sac_id) ?>" class="btn btn-primary mb-3">Ajouter un médicament</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Quantité</th>
                <th>Date d'expiration</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($medicaments) > 0): ?>
                <?php foreach ($medicaments as $med): ?>
                    <tr>
                        <td><?= htmlspecialchars($med['nom'] ?? 'Inconnu') ?></td>
                        <td><?= htmlspecialchars($med['description'] ?? 'Aucune description') ?></td>
                        <td><?= htmlspecialchars($med['quantite'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($med['date_expiration'] ?? 'Non spécifiée') ?></td>
                        <td>
                            <a href="edit.php?id=<?= $med['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <a href="delete.php?id=<?= $med['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer ce médicament ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Aucun médicament trouvé pour ce sac.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
