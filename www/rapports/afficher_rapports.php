<?php
include '../includes/db.php';

// Récupérer tous les rapports
$stmt = $pdo->query("
    SELECT rapports_utilisation.*, sacs_medicaux.nom AS nom_sac 
    FROM rapports_utilisation 
    JOIN sacs_medicaux ON rapports_utilisation.sac_id = sacs_medicaux.id
    ORDER BY date_rapport DESC
");
$rapports = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Rapports d'Utilisation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<style>
    /* Style pour le menu */
     .navbar .btn {
    min-width: 150px; /* Largeur minimale pour uniformité */
    max-width: auto; /* Laisse la largeur s'ajuster dynamiquement */
    text-align: center; /* Centre le texte */
    white-space: nowrap; /* Empêche le retour à la ligne */
    display: inline-flex; /* Permet une meilleure gestion des espaces */
    align-items: center; /* Aligne le texte et l'icône verticalement */
    justify-content: center; /* Centre le contenu horizontalement */
    padding: 10px 15px; /* Ajuste l'espacement interne */
}

.navbar .btn i {
    margin-right: 8px; /* Espace entre l'icône et le texte */
}
        .dropdown-item i {
    margin-right: 8px; /* Espace entre l'icône et le texte */
}
        .dropdown-toggle {
    border: none; /* Supprime la bordure */
    box-shadow: none; /* Supprime l'ombre */
}
.dropdown-toggle:focus {
    outline: none; /* Supprime l'effet de focus */
    box-shadow: none; /* Supprime l'ombre au focus */
}
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
<!-- Inclure le menu -->
<?php include '../menus/menu_usersmanage.php'; ?>
<div class="container mt-5">
    <h1 class="text-center mb-4">Rapports d'Utilisation</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sac</th>
                <th>Nom de l'Événement</th>
                <th>Utilisateur</th>
                <th>Matériel Utilisé</th>
                <th>Observations</th>
                <th>Date de Saisie</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rapports as $rapport): ?>
                <tr>
                    <td><?= htmlspecialchars($rapport['nom_sac']) ?></td>
                    <td><?= htmlspecialchars($rapport['nom_evenement']) ?></td>
                    <td><?= htmlspecialchars($rapport['utilisateur']) ?></td>
                    <td><?= nl2br(htmlspecialchars($rapport['materiels_utilises'])) ?></td>
                    <td><?= nl2br(htmlspecialchars($rapport['observations'])) ?></td>
                    <td><?= htmlspecialchars($rapport['date_saisie']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
