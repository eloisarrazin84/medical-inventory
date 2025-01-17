<?php
include '../includes/db.php';

// Vérifier si un ID est fourni
if (!isset($_GET['id'])) {
    die('Erreur : Aucun lot spécifié.');
}

$lot_id = (int)$_GET['id'];

// Récupérer les informations du lot
$stmt = $pdo->prepare("SELECT * FROM lots WHERE id = ?");
$stmt->execute([$lot_id]);
$lot = $stmt->fetch();

if (!$lot) {
    die('Erreur : Lot introuvable.');
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $description = htmlspecialchars($_POST['description']);

    // Mettre à jour les informations du lot
    $stmt = $pdo->prepare("UPDATE lots SET nom = ?, description = ? WHERE id = ?");
    if ($stmt->execute([$nom, $description, $lot_id])) {
        header('Location: manage_lots.php?sac_id=' . $_GET['sac_id']); // Redirection après modification
        exit;
    } else {
        die('Erreur : Impossible de modifier le lot.');
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Modifier un Lot</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Modifier le Lot</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom du Lot</label>
            <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($lot['nom']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control"><?= htmlspecialchars($lot['description']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="manage_lots.php?sac_id=<?= $_GET['sac_id'] ?>" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>
