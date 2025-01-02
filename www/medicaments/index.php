<?php
include '../includes/db.php';
include '../includes/auth.php';

// Vérifier si `sac_id` est défini dans l'URL
if (!isset($_GET['sac_id']) || empty($_GET['sac_id'])) {
    // Rediriger vers la page de choix des sacs
    header('Location: choisir_sac.php');
    exit;
}

$sac_id = $_GET['sac_id'];

// Récupérer les informations du sac sélectionné
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
    <title>Médicaments - Gestion des Sacs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"> <!-- Animate.css -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"> <!-- AOS -->
<style>
    /* Style pour le menu */
    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1030;
        background-color: rgba(0, 0, 0, 0.8); /* Transparence avec fond noir */
    }

    .navbar-brand img {
        height: 50px;
    }

    .btn {
        border-radius: 30px; /* Boutons arrondis */
        font-weight: bold; /* Texte en gras */
        transition: all 0.3s ease-in-out; /* Animation fluide */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Ombre légère */
    }

    .btn:hover {
        transform: translateY(-3px); /* Effet de levée */
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2); /* Ombre plus forte */
        color: #fff !important; /* Texte blanc au survol */
    }
</style>
</head>
<body>
<!-- Inclure le menu -->
<?php include '../menus/menu_medicaments.php'; ?>
<div class="container mt-5">
    <h1>Gestion des Médicaments</h1>
     <a href="choisir_sac.php>" class="btn btn-primary mb-3">Choisir un autre sac</a>
    <a href="add.php?sac_id=<?= htmlspecialchars($sac_id) ?>" class="btn btn-primary mb-3">Ajouter un médicament</a>
    
    <!-- Table des médicaments -->
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
