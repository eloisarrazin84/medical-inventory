<?php
include '../includes/db.php';

// Vérifiez si l'ID du consommable est fourni
if (!isset($_GET['id'])) {
    die('Erreur : Aucun consommable spécifié.');
}

$cons_id = (int)$_GET['id'];

// Récupérez les informations du consommable
$stmt = $pdo->prepare("SELECT * FROM consommables WHERE id = ?");
$stmt->execute([$cons_id]);
$cons = $stmt->fetch();

if (!$cons) {
    die('Erreur : Consommable introuvable.');
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $description = htmlspecialchars($_POST['description']);
    $date_expiration = $_POST['date_expiration'];
    $quantite = (int)$_POST['quantite'];

    $stmt = $pdo->prepare("UPDATE consommables SET nom = ?, description = ?, date_expiration = ?, quantite = ? WHERE id = ?");
    if ($stmt->execute([$nom, $description, $date_expiration, $quantite, $cons_id])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    } else {
        die('Erreur : Impossible de modifier le consommable.');
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Modifier un Consommable</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Modifier le Consommable</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom du Consommable</label>
            <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($cons['nom']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control"><?= htmlspecialchars($cons['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="date_expiration" class="form-label">Date d'Expiration</label>
            <input type="date" id="date_expiration" name="date_expiration" class="form-control" value="<?= htmlspecialchars($cons['date_expiration']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="quantite" class="form-label">Quantité</label>
            <input type="number" id="quantite" name="quantite" class="form-control" value="<?= $cons['quantite'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="manage_lots.php?sac_id=<?= $_GET['sac_id'] ?>" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>
