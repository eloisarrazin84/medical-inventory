<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    
    $stmt = $pdo->prepare("INSERT INTO evenements (nom, latitude, longitude, date_debut, date_fin) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $latitude, $longitude, $date_debut, $date_fin]);

    header("Location: carte.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un événement</title>
</head>
<body>
    <h2>Ajouter un Dispositif Médical</h2>
    <form action="" method="post">
        <label>Nom de l'événement :</label>
        <input type="text" name="nom" required>
        
        <label>Latitude :</label>
        <input type="text" name="latitude" required>

        <label>Longitude :</label>
        <input type="text" name="longitude" required>

        <label>Date de début :</label>
        <input type="datetime-local" name="date_debut" required>

        <label>Date de fin :</label>
        <input type="datetime-local" name="date_fin" required>

        <button type="submit">Ajouter</button>
    </form>
</body>
</html>
