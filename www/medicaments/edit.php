<?php
include '../includes/db.php';

$id = $_GET['id'];

// Récupérer les informations du médicament
$stmt = $pdo->prepare("SELECT * FROM medicaments WHERE id = ?");
$stmt->execute([$id]);
$med = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $quantite = $_POST['quantite'];
    $date_expiration = $_POST['date_expiration'];
    $numero_lot = $_POST['numero_lot'];
    $type_produit = $_POST['type_produit'];

    $stmt = $pdo->prepare("UPDATE medicaments SET nom = ?, description = ?, quantite = ?, date_expiration = ?, numero_lot = ?, type_produit = ? WHERE id = ?");
    $stmt->execute([$nom, $description, $quantite, $date_expiration, $numero_lot, $type_produit, $id]);

    header("Location: index.php?sac_id={$med['sac_id']}");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Modifier un Médicament</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"> <!-- Animate.css -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"> <!-- AOS -->
     <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Modifier un Médicament</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom du médicament</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($med['nom']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($med['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="quantite" class="form-label">Quantité</label>
            <input type="number" class="form-control" id="quantite" name="quantite" value="<?= $med['quantite'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="date_expiration" class="form-label">Date d'expiration</label>
            <input type="date" class="form-control" id="date_expiration" name="date_expiration" value="<?= $med['date_expiration'] ?>">
        </div>
        <div class="mb-3">
            <label for="numero_lot" class="form-label">Numéro de Lot</label>
            <input type="text" class="form-control" id="numero_lot" name="numero_lot" value="<?= htmlspecialchars($med['numero_lot']) ?>">
        </div>
        <div class="mb-3">
            <label for="type_produit" class="form-label">Type de Médicament</label>
            <select class="form-select" id="type_produit" name="type_produit" required>
                <option value="Injectable" <?= $med['type_produit'] === 'Injectable' ? 'selected' : '' ?>>Injectable</option>
                <option value="PER OS" <?= $med['type_produit'] === 'PER OS' ? 'selected' : '' ?>>PER OS</option>
                <option value="Inhalable" <?= $med['type_produit'] === 'Inhalable' ? 'selected' : '' ?>>Inhalable</option>
                <option value="Buvable" <?= $med['type_produit'] === 'Buvable' ? 'selected' : '' ?>>Buvable</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="index.php?sac_id=<?= $med['sac_id'] ?>" class="btn btn-secondary">Annuler</a>
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

