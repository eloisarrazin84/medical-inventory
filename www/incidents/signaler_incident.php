<?php
require '../vendor/autoload.php'; // Assurez-vous que le chemin est correct

include '../includes/db.php';
include '../includes/send_email.php'; // Assurez-vous que ce fichier contient la fonction sendEmail

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
        // Enregistrer l'incident dans la base de données
        $stmt = $pdo->prepare("
            INSERT INTO incidents (type_incident, reference_id, nom_evenement, nom_personne, description, statut) 
            VALUES (?, ?, ?, ?, ?, 'Non Résolu')
        ");
        $stmt->execute([$type_incident, $reference_id, $nom_evenement, $nom_personne, $description]);

        // Préparation de l'e-mail
        $sujet = "Nouvel incident signalé : $type_incident";
        $message = "
        <div style='font-family: Arial, sans-serif; color: #333; line-height: 1.6;'>
            <div style='text-align: center; padding: 10px 0;'>
                <img src='https://outdoorsecours.fr/wp-content/uploads/2023/07/thumbnail_image001-1.png' alt='Outdoor Secours' style='height: 100px;'>
            </div>
            <h2 style='color: #0056b3; text-align: center;'>Un nouvel incident a été signalé</h2>
            <div style='border: 1px solid #ddd; border-radius: 10px; padding: 20px;'>
                <p><strong>Type d'incident :</strong> $type_incident</p>
                <p><strong>Nom du sac :</strong> {$sac['nom']}</p>
                <p><strong>Nom de l'événement :</strong> $nom_evenement</p>
                <p><strong>Signalé par :</strong> $nom_personne</p>
                <p><strong>Description :</strong> $description</p>
            </div>
            <p style='text-align: center; margin-top: 20px; font-size: 12px; color: #999;'>Outdoor Secours © " . date('Y') . " - Tous droits réservés.</p>
        </div>
        ";

        // Appel de la fonction d'envoi d'e-mail
        if (sendEmail("contact@outdoorsecours.fr", $sujet, $message)) {
            // Redirection avec succès
            header("Location: confirmation_signalement.php?message=Incident signalé et notification envoyée");
        } else {
            header("Location: confirmation_signalement.php?message=Incident signalé mais échec de la notification");
        }
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
