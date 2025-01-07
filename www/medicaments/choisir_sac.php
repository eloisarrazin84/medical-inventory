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
<?php include '../menus/menu_usersmanage.php'; ?>

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
