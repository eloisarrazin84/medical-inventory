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
        .badge { padding: 0.5em 0.75em; font-size: 0.9em; }
        .btn { margin-right: 5px; }
        .table-responsive { margin-top: 20px; }
    </style>
</head>
<body>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
    });
</script>
</body>
</html>
