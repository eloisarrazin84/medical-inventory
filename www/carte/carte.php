<?php
include '../includes/db.php';

header('Content-Type: application/json');

$stmt = $pdo->query("SELECT id, nom, latitude, longitude, statut FROM evenements WHERE statut = 'En cours'");
$evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($evenements);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte des dispositifs médicaux</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <style>
        #map { height: 600px; width: 100%; }
    </style>
</head>
<body>
    <h2 class="text-center">Carte des Dispositifs Médicaux en Cours</h2>
    <div id="map"></div>

    <script>
        // Initialiser la carte
        var map = L.map('map').setView([46.603354, 1.888334], 6); // Centre sur la France

        // Ajouter un fond de carte OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Charger les événements depuis le fichier PHP
        fetch('carte.php')
            .then(response => response.json())
            .then(data => {
                data.forEach(event => {
                    L.marker([event.latitude, event.longitude])
                        .addTo(map)
                        .bindPopup(`<b>${event.nom}</b><br>Statut: ${event.statut}`);
                });
            })
            .catch(error => console.error('Erreur de chargement des événements:', error));
    </script>
</body>
</html>
