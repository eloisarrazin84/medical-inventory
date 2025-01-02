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

    $stmt = $pdo->prepare("UPDATE medicaments SET nom = ?, description = ?, quantite = ?, date_expiration = ? WHERE id = ?");
    $stmt->execute([$nom, $description, $quantite, $date_expiration, $id]);

    header("Location: index.php?sac_id={$med['sac_id']}");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Modifier un Médicament</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="index.php?sac_id=<?= $med['sac_id'] ?>" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>
