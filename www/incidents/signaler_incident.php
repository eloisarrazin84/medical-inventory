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
            <input type="text" class="form-control" id="reference_id" name="reference_id" value="<?= htmlspecialchars($reference_id ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description du problème</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Soumettre</button>
        <a href="../sacs/inventaire_sac.php?id=<?= $reference_id ?>" class="btn btn-secondary">Annuler</a>
    </form>
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
