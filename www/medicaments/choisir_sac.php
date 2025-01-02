<?php
include '../includes/db.php';
include '../includes/auth.php';

// Récupérer tous les sacs disponibles
$stmt = $pdo->query("SELECT * FROM sacs_medicaux");
$sacs = $stmt->fetchAll();

// Vérifier s'il y a au moins un sac disponible
if (count($sacs) === 0) {
    die('Erreur : Aucun sac médical trouvé.');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Choisir un Sac</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Choisir un Sac Médical</h1>
    <p>Sélectionnez un sac pour gérer ses médicaments.</p>
    <form method="GET" action="index.php">
        <label for="sac_id" class="form-label">Sélectionner un sac :</label>
        <select name="sac_id" id="sac_id" class="form-select mb-3" required>
            <?php foreach ($sacs as $sac): ?>
                <option value="<?= htmlspecialchars($sac['id']) ?>">
                    <?= htmlspecialchars($sac['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Accéder au Sac</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
