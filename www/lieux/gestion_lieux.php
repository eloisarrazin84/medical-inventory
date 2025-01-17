<?php
include '../includes/db.php';
include '../includes/auth.php';
include '../session_manager.php';

// Vérifiez si l'utilisateur est connecté
check_auth();
// Ajouter un lieu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_lieu'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("INSERT INTO lieux_stockage (nom, description) VALUES (?, ?)");
    $stmt->execute([$nom, $description]);

    header('Location: gestion_lieux.php');
    exit;
}

// Supprimer un lieu
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    $stmt = $pdo->prepare("DELETE FROM lieux_stockage WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: gestion_lieux.php');
    exit;
}

// Récupérer tous les lieux
$stmt = $pdo->query("SELECT * FROM lieux_stockage");
$lieux = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Gestion des Lieux de Stockage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"> <!-- Animate.css -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"> <!-- AOS -->
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
<!-- Inclure le menu -->
<?php include '../menus/menu_usersmanage.php'; ?>
<div class="container mt-5">
    <h1>Gestion des Lieux de Stockage</h1>

    <!-- Formulaire d'ajout de lieu -->
    <form method="POST" class="mb-5">
        <h2>Ajouter un Lieu</h2>
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description"></textarea>
        </div>
        <button type="submit" name="ajouter_lieu" class="btn btn-primary">Ajouter</button>
    </form>

    <!-- Liste des lieux -->
    <h2>Liste des Lieux</h2>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($lieux) > 0): ?>
            <?php foreach ($lieux as $lieu): ?>
                <tr>
                    <td><?= htmlspecialchars($lieu['nom']) ?></td>
                    <td><?= htmlspecialchars($lieu['description'] ?? 'Aucune description') ?></td>
                    <td>
                        <a href="gestion_lieux.php?delete_id=<?= $lieu['id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Voulez-vous vraiment supprimer ce lieu ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" class="text-center">Aucun lieu de stockage trouvé.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
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
</html>
