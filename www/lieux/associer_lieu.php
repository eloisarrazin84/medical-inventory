<?php
include '../includes/db.php';
include '../includes/auth.php';

$sac_id = $_GET['sac_id'] ?? null;

// Vérifier si le sac existe
$stmt = $pdo->prepare("SELECT * FROM sacs_medicaux WHERE id = ?");
$stmt->execute([$sac_id]);
$sac = $stmt->fetch();

if (!$sac) {
    die('Sac médical introuvable.');
}

// Mettre à jour le lieu du sac
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lieu_id = $_POST['lieu_id'];

    $stmt = $pdo->prepare("UPDATE sacs_medicaux SET lieu_id = ? WHERE id = ?");
    $stmt->execute([$lieu_id, $sac_id]);

    header("Location: ../sacs/index.php");
    exit;
}

// Récupérer les lieux disponibles
$stmt = $pdo->query("SELECT * FROM lieux_stockage");
$lieux = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Associer un Lieu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"> <!-- Animate.css -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"> <!-- AOS -->
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Associer un Lieu</h1>
    <p>Sac : <?= htmlspecialchars($sac['nom']) ?></p>

    <form method="POST">
        <div class="mb-3">
            <label for="lieu_id" class="form-label">Lieu</label>
            <select class="form-select" id="lieu_id" name="lieu_id" required>
                <option value="">Sélectionnez un lieu</option>
                <?php foreach ($lieux as $lieu): ?>
                    <option value="<?= $lieu['id'] ?>" <?= $sac['lieu_id'] == $lieu['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($lieu['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Associer</button>
        <a href="../sacs/index.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000, // Durée de l'animation (en ms)
    });
</script>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
</body>
</html>
