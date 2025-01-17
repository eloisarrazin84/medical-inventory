<?php
include '../includes/db.php';

// Vérifier si l'ID du sac est fourni
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

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $utilisateur = $_POST['utilisateur'];
    $nom_evenement = $_POST['nom_evenement'];
    $materiels_utilises = $_POST['materiels_utilises'];
    $observations = $_POST['observations'];

    try {
        // Insérer le rapport dans la base de données
        $stmt = $pdo->prepare("
            INSERT INTO rapports_utilisation (sac_id, nom_evenement, utilisateur, materiels_utilises, observations) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$sac_id, $nom_evenement, $utilisateur, $materiels_utilises, $observations]);

        // Redirection après soumission
        header("Location: ../rapports/confirmation_rapport.php");
        exit;
    } catch (PDOException $e) {
        die('Erreur lors de l\'enregistrement : ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Déclarer un Rapport</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Déclarer un Rapport pour <?= htmlspecialchars($sac['nom']) ?></h1>
    <form method="POST">
        <div class="mb-3">
            <label for="utilisateur" class="form-label">Nom du Médecin</label>
            <input type="text" class="form-control" id="utilisateur" name="utilisateur" placeholder="Ex : Dr. Jean Dupont" required>
        </div>
        <div class="mb-3">
            <label for="nom_evenement" class="form-label">Nom de l'Événement</label>
            <input type="text" class="form-control" id="nom_evenement" name="nom_evenement" placeholder="Ex : Marathon de Paris" required>
        </div>
        <div class="mb-3">
            <label for="materiels_utilises" class="form-label">Matériel Utilisé</label>
            <textarea class="form-control" id="materiels_utilises" name="materiels_utilises" rows="4" placeholder="Listez les articles utilisés..." required></textarea>
        </div>
        <div class="mb-3">
            <label for="observations" class="form-label">Observations</label>
            <textarea class="form-control" id="observations" name="observations" rows="4" placeholder="Ajoutez des remarques supplémentaires (facultatif)..."></textarea>
        </div>
        <button type="submit" class="btn btn-success">Soumettre</button>
        <a href="javascript:history.back()" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
