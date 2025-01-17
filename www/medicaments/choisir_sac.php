<?php
include '../includes/db.php';
include '../includes/auth.php';

// Traitement de la redirection après soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['sac_id'], $_GET['action'])) {
    $sac_id = htmlspecialchars($_GET['sac_id']);
    $action = htmlspecialchars($_GET['action']);

    if ($action === 'medicaments') {
        header("Location: ../medicaments/index.php?sac_id=$sac_id");
    } elseif ($action === 'lots') {
        header("Location: ../lots/manage_lots.php?sac_id=$sac_id");
    } else {
        die('Erreur : Action invalide.');
    }
    exit;
}

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
<div class="page-content">
<div class="container mt-5">
    <h1>Choisir un Sac Médical</h1>
    <p>Sélectionnez un sac pour gérer ses médicaments ou ses lots.</p>
    <form method="GET" action="">
        <label for="sac_id" class="form-label">Sélectionner un sac :</label>
        <select name="sac_id" id="sac_id" class="form-select mb-3" required>
            <?php foreach ($sacs as $sac): ?>
                <option value="<?= htmlspecialchars($sac['id']) ?>">
                    <?= htmlspecialchars($sac['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="action" class="form-label">Action :</label>
        <select name="action" id="action" class="form-select mb-3" required>
            <option value="medicaments">Gérer les Médicaments</option>
            <option value="lots">Gérer les Lots et Consommables</option>
        </select>

        <button type="submit" class="btn btn-primary">Accéder</button>
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
