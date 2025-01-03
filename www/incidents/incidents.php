<?php
include '../includes/db.php';
include '../includes/auth.php';

// Récupérer les incidents avec le nom des sacs associés
$stmt = $pdo->query("
    SELECT incidents.*, sacs_medicaux.nom AS sac_nom 
    FROM incidents 
    LEFT JOIN sacs_medicaux 
    ON incidents.reference_id = sacs_medicaux.id 
    ORDER BY incidents.date_signalement DESC
");
$incidents = $stmt->fetchAll();

// Fonction pour le style des badges
function getBadgeClass($statut) {
    switch ($statut) {
        case 'Non Résolu':
            return 'badge-danger';
        case 'En Cours':
            return 'badge-warning';
        case 'Résolu':
            return 'badge-success';
        default:
            return 'badge-secondary';
    }
}
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
    .badge {
    padding: 0.5em 0.75em;
    font-size: 0.9em;
    color: black !important; /* Texte noir */
    border-radius: 0.25rem; /* Coins arrondis */
    font-weight: bold; /* Texte en gras */
}

.badge-danger {
    background-color: #dc3545 !important; /* Rouge */
    color: #fff !important; /* Texte blanc pour contraste */
}

.badge-warning {
    background-color: #ffc107 !important; /* Jaune */
    color: #212529 !important; /* Texte noir */
}

.badge-success {
    background-color: #28a745 !important; /* Vert */
    color: #fff !important; /* Texte blanc */
}

    .btn {
        margin-right: 5px;
    }

    .table-responsive {
        margin-top: 20px;
    }
</style>
</head>
<body>
    <!-- Inclure le menu -->
<?php include '../menus/menu_usersmanage.php'; ?>
<div class="container mt-5">
    <h1 class="mb-4">Suivi des Incidents</h1>

    <!-- Barre de recherche -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Rechercher par description ou nom du sac">
            </div>
            <div class="col-md-4">
                <select name="statut" class="form-control">
                    <option value="">Tous les statuts</option>
                    <option value="Non Résolu">Non Résolu</option>
                    <option value="En Cours">En Cours</option>
                    <option value="Résolu">Résolu</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
        </div>
    </form>

    <!-- Tableau des incidents -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Référence (Nom du Sac)</th>
                    <th>Description</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($incidents)): ?>
                    <?php foreach ($incidents as $incident): ?>
                        <tr>
                            <td><?= htmlspecialchars($incident['type_incident']) ?></td>
                            <td><?= htmlspecialchars($incident['sac_nom'] ?? 'Non spécifié') ?></td>
                            <td><?= htmlspecialchars($incident['description']) ?></td>
                            <td>
                                <span class="badge <?= getBadgeClass($incident['statut']) ?>">
                                    <?= htmlspecialchars($incident['statut']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($incident['date_signalement']) ?></td>
                            <td>
                                <a href="changer_statut.php?id=<?= $incident['id'] ?>&statut=En Cours" 
                                   class="btn btn-warning btn-sm" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir marquer cet incident comme "En Cours" ?');">
                                    En Cours
                                </a>
                                <a href="changer_statut.php?id=<?= $incident['id'] ?>&statut=Résolu" 
                                   class="btn btn-success btn-sm" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir marquer cet incident comme "Résolu" ?');">
                                    Résolu
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Aucun incident trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bouton d'exportation -->
    <a href="exporter_incidents.php" class="btn btn-outline-secondary">Exporter en CSV</a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000 });
</script>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
</body>
</html>
