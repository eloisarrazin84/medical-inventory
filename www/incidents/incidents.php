<?php
include '../includes/db.php';
include '../includes/auth.php';

// Requête pour récupérer les incidents avec le nom du sac
$stmt = $pdo->query("
    SELECT incidents.*, 
           sacs_medicaux.nom AS sac_nom 
    FROM incidents
    LEFT JOIN sacs_medicaux ON incidents.reference_id = sacs_medicaux.id
    ORDER BY incidents.date_signalement DESC
");
$incidents = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Gestion des Incidents</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
    <style>
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .navbar-brand img {
            height: 50px;
        }

        .btn {
            border-radius: 30px;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
            color: #fff !important;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Suivi des Incidents</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Type</th>
                <th>Référence</th>
                <th>Description</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($incidents as $incident): ?>
                <tr>
                    <td><?= htmlspecialchars($incident['type_incident']) ?></td>
                    <td><?= htmlspecialchars($incident['sac_nom'] ?? 'Non spécifié') ?></td>
                    <td><?= htmlspecialchars($incident['description']) ?></td>
                    <td><?= htmlspecialchars($incident['statut']) ?></td>
                    <td><?= htmlspecialchars($incident['date_signalement']) ?></td>
                    <td>
                        <a href="changer_statut.php?id=<?= $incident['id'] ?>&statut=En Cours" class="btn btn-warning btn-sm">En Cours</a>
                        <a href="changer_statut.php?id=<?= $incident['id'] ?>&statut=Résolu" class="btn btn-success btn-sm">Résolu</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
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
