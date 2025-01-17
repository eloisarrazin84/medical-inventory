<?php
include '../includes/db.php';

$sac_id = $_GET['sac_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $quantite = $_POST['quantite'];
    $date_expiration = $_POST['date_expiration'];
    $numero_lot = $_POST['numero_lot'];
    $type_medicament = $_POST['type_medicament'];

    $stmt = $pdo->prepare("INSERT INTO medicaments (nom, description, quantite, date_expiration, sac_id, numero_lot, type_produit) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $description, $quantite, $date_expiration, $sac_id, $numero_lot, $type_medicament]);

    header("Location: index.php?sac_id=$sac_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Ajouter un Médicament</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Ajouter un Médicament</h1>
    <form method="POST">
        <!-- Nom du médicament -->
        <div class="mb-3">
            <label for="nom" class="form-label">Nom du médicament</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description"></textarea>
        </div>
        <!-- Quantité -->
        <div class="mb-3">
            <label for="quantite" class="form-label">Quantité</label>
            <input type="number" class="form-control" id="quantite" name="quantite" required>
        </div>
        <!-- Date d'expiration -->
        <div class="mb-3">
            <label for="date_expiration" class="form-label">Date d'expiration</label>
            <input type="date" class="form-control" id="date_expiration" name="date_expiration">
        </div>
        <!-- Numéro de Lot -->
        <div class="mb-3">
            <label for="numero_lot" class="form-label">Numéro de Lot</label>
            <input type="text" class="form-control" id="numero_lot" name="numero_lot">
        </div>
        <!-- Type de Médicament -->
        <div class="mb-3">
            <label for="type_medicament" class="form-label">Type de Médicament</label>
            <select class="form-control" id="type_medicament" name="type_medicament" required>
                <option value="">-- Sélectionner --</option>
                <option value="Injectable">Injectable</option>
                <option value="PER OS">PER OS</option>
                <option value="Inhalable">Inhalable</option>
                <option value="Buvable">Buvable</option>
            </select>
        </div>
        <!-- Boutons -->
        <button type="submit" class="btn btn-primary">Ajouter</button>
        <a href="index.php?sac_id=<?= $sac_id ?>" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    $(document).ready(function () {
        // Charger la liste des médicaments pour l'autocomplétion
        $.get('list.txt', function (data) {
            let medicaments = data.split('\n')
                .map(line => line.split(',')[0].trim()) // Extraire uniquement le nom
                .filter(line => line !== ''); // Filtrer les lignes vides

            $("#nom").autocomplete({
                source: function (request, response) {
                    const filtered = medicaments.filter(item => item.toLowerCase().includes(request.term.toLowerCase()));
                    response(filtered.slice(0, 10)); // Limiter à 10 résultats
                },
                minLength: 2 // Déclencher après 2 caractères
            });
        }).fail(function () {
            console.error('Erreur lors du chargement de list.txt.');
        });
    });
</script>
</body>
</html>
