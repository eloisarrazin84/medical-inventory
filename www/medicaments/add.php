<?php
include '../includes/db.php';

$sac_id = $_GET['sac_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $quantite = $_POST['quantite'];
    $date_expiration = $_POST['date_expiration'];

    $stmt = $pdo->prepare("INSERT INTO medicaments (nom, description, quantite, date_expiration, sac_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $description, $quantite, $date_expiration, $sac_id]);

    header("Location: index.php?sac_id=$sac_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Ajouter un Médicament</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"> <!-- AOS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"> <!-- jQuery UI -->
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
    <h1>Ajouter un Médicament</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom du médicament</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description"></textarea>
        </div>
        <div class="mb-3">
            <label for="quantite" class="form-label">Quantité</label>
            <input type="number" class="form-control" id="quantite" name="quantite" required>
        </div>
        <div class="mb-3">
            <label for="date_expiration" class="form-label">Date d'expiration</label>
            <input type="date" class="form-control" id="date_expiration" name="date_expiration">
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
        <a href="index.php?sac_id=<?= $sac_id ?>" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    // Charger la liste des médicaments depuis list.txt
    $(document).ready(function () {
        $.get('list.txt', function (data) {
            // Diviser les lignes en tableau et supprimer les espaces inutiles
            let medicaments = data.split('\n').map(line => line.trim()).filter(line => line !== '');

            // Activer l'autocomplétion sur le champ Nom
            $("#nom").autocomplete({
                source: medicaments
            });
        });
    });

    AOS.init({
        duration: 1000, // Durée de l'animation (en ms)
    });
</script>
</body>
</html>

