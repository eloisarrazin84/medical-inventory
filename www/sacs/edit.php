<?php
include '../includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM sacs_medicaux WHERE id = ?");
$stmt->execute([$id]);
$sac = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("UPDATE sacs_medicaux SET nom = ?, description = ? WHERE id = ?");
    $stmt->execute([$nom, $description, $id]);

    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Modifier un Sac Médical</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"> <!-- Animate.css -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"> <!-- AOS -->
    <link href="../css/styles.css" rel="stylesheet">

</head>
<body>
<div class="container mt-5">
    <h1>Modifier le Sac Médical</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom du Sac</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($sac['nom']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($sac['description']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="index.php" class="btn btn-secondary">Annuler</a>
    </form>
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
