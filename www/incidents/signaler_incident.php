<?php
include '../includes/db.php';

// Récupérer l'ID du sac depuis l'URL ou un paramètre
$sac_id = $_GET['sac_id'] ?? null;

if (!$sac_id) {
    die('Erreur : Aucun sac sélectionné.');
}

// Vérifier si le sac existe
$stmt = $pdo->prepare("SELECT * FROM sacs_medicaux WHERE id = ?");
$stmt->execute([$sac_id]);
$sac = $stmt->fetch();

if (!$sac) {
    die('Erreur : Sac médical introuvable.');
}

// Vérifier si un incident est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type_incident = $_POST['type_incident'];
    $reference_id = $sac_id; // Utiliser l'ID du sac ici
    $nom_evenement = $_POST['nom_evenement'];
    $nom_personne = $_POST['nom_personne'];
    $description = $_POST['description'];

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Signaler un Incident</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="type_incident" class="form-label">Type d'Incident</label>
            <select class="form-control" id="type_incident" name="type_incident" required>
                <option value="Sac">Sac</option>
                <option value="Médicament">Médicament</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="reference_id" class="form-label">Référence (Nom du Sac)</label>
            <input type="text" class="form-control" id="reference_id" value="<?= htmlspecialchars($sac['nom']) ?>" readonly>
        </div>
        <input type="hidden" name="reference_id" value="<?= $sac_id ?>">
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
            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Décrivez le problème rencontré..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Soumettre</button>
        <a href="javascript:history.back()" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
