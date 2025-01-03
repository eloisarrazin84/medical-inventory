<?php
include '../includes/db.php';

$type_incident = $_GET['type'] ?? 'Sac'; // Par défaut, on suppose qu'il s'agit d'un sac
$reference_id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type_incident = $_POST['type_incident'];
    $reference_id = $_POST['reference_id'];
    $description = $_POST['description'];
    $utilisateur_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO incidents (type_incident, reference_id, description, utilisateur_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$type_incident, $reference_id, $description, $utilisateur_id]);

    header("Location: ../sacs/inventaire_sac.php?id={$reference_id}");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Signaler un Incident</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Signaler un Incident</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="type_incident" class="form-label">Type d'Incident</label>
            <select class="form-control" id="type_incident" name="type_incident" required>
                <option value="Sac" <?= $type_incident === 'Sac' ? 'selected' : '' ?>>Sac</option>
                <option value="Médicament" <?= $type_incident === 'Médicament' ? 'selected' : '' ?>>Médicament</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="reference_id" class="form-label">Référence</label>
            <input type="text" class="form-control" id="reference_id" name="reference_id" value="<?= htmlspecialchars($reference_id) ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description du problème</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Soumettre</button>
        <a href="../sacs/inventaire_sac.php?id=<?= $reference_id ?>" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>
