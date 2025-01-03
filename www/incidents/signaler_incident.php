<?php
include '../includes/db.php';

// Récupérer l'ID du sac depuis l'URL ou un paramètre
$sac_id = $_GET['sac_id'] ?? null;

if (!$sac_id) {
    die('Erreur : Aucun sac sélectionné.');
}

// Vérifier si un incident est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type_incident = $_POST['type_incident'];
    $reference_id = $_POST['reference_id'];
    $nom_evenement = $_POST['nom_evenement'];
    $nom_personne = $_POST['nom_personne'];
    $description = $_POST['description'];

    // Enregistrer l'incident dans la base de données
    try {
        $stmt = $pdo->prepare("
            INSERT INTO incidents (type_incident, reference_id, nom_evenement, nom_personne, description, statut) 
            VALUES (?, ?, ?, ?, ?, 'Non Résolu')
        ");
        $stmt->execute([$type_incident, $reference_id, $nom_evenement, $nom_personne, $description]);

        // Redirection après soumission
        header("Location: confirmation_signalement.php");
        exit;
    } catch (PDOException $e) {
        die('Erreur lors de l\'enregistrement : ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Signaler un Incident</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
</head>
<body>
<div class="container mt-5">
    <h1>Signaler un Incident</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="type_incident" class="form-label">Type d'Incident</label>
            <select class="form-control" id="type_incident" name="type_incident" required>
                <option value="Sac">Sac</option>
                <option value="Médicament">Médicament</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="reference_id" class="form-label">Référence</label>
            <input type="text" class="form-control" id="reference_id" name="reference_id" value="<?= htmlspecialchars($sac_id) ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="nom_evenement" class="form-label">Nom de l'Événement</label>
            <input type="text" class="form-control" id="nom_evenement" name="nom_evenement" placeholder="Ex: Marathon de Paris" required>
        </div>
        <div class="mb-3">
            <label for="nom_personne" class="form-label">Nom de la Personne</label>
            <input type="text" class="form-control" id="nom_personne" name="nom_personne" placeholder="Ex: Jean Dupont" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description du problème</label>
            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Soumettre</button>
        <a href="index.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
    });
</script>
</body>
</html>
