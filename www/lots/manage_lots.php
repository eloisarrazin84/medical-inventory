<?php
include '../includes/db.php';
include '../includes/auth.php';
include '../session_manager.php';

// Vérifiez si l'utilisateur est connecté
check_auth();

// Récupérer l'ID du sac
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

// Ajouter un nouveau lot
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_lot'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $description = htmlspecialchars($_POST['description']);

    $stmt = $pdo->prepare("INSERT INTO lots (sac_id, nom, description) VALUES (?, ?, ?)");
    $stmt->execute([$sac_id, $nom, $description]);

    header("Location: manage_lots.php?sac_id=$sac_id");
    exit;
}

// Ajouter un consommable à un lot
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_consumable'])) {
    $lot_id = $_POST['lot_id'];
    $nom = htmlspecialchars($_POST['nom']);
    $description = htmlspecialchars($_POST['description']);
    $date_expiration = $_POST['date_expiration'];
    $quantite = (int)$_POST['quantite'];

    $stmt = $pdo->prepare("INSERT INTO consommables (lot_id, nom, description, date_expiration, quantite) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$lot_id, $nom, $description, $date_expiration, $quantite]);

    header("Location: manage_lots.php?sac_id=$sac_id");
    exit;
}

// Récupérer les lots et leurs consommables
$stmt = $pdo->prepare("SELECT * FROM lots WHERE sac_id = ?");
$stmt->execute([$sac_id]);
$lots = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Gestion des Lots - <?= htmlspecialchars($sac['nom']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"> <!-- Animate.css -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"> <!-- AOS -->
<style>
    /* Style pour le menu */
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

   <div class="container mt-3">
    <h1 class="mb-4">Gestion des Lots : <?= htmlspecialchars($sac['nom']) ?></h1>

    <!-- Ajouter un nouveau lot -->
    <div class="card mb-3">
        <div class="card-header">Ajouter un Lot</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom du Lot</label>
                    <input type="text" name="nom" id="nom" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description (facultatif)</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>
                <button type="submit" name="add_lot" class="btn btn-primary">Ajouter le Lot</button>
            </form>
        </div>
    </div>

    <!-- Liste des lots -->
    <?php if (!empty($lots)): ?>
        <?php foreach ($lots as $lot): ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><?= htmlspecialchars($lot['nom']) ?></span>
                    <div>
                        <!-- Boutons Modifier et Supprimer pour le lot -->
                        <a href="edit_lot.php?id=<?= $lot['id'] ?>&sac_id=<?= $sac_id ?>" class="btn btn-warning btn-sm me-2">Modifier</a>
                        <a href="delete_lot.php?id=<?= $lot['id'] ?>&sac_id=<?= $sac_id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce lot ?')">Supprimer</a>
                        <button class="btn btn-sm btn-success" data-bs-toggle="collapse" data-bs-target="#lot-<?= $lot['id'] ?>">Voir Consommables</button>
                    </div>
                </div>
                <div class="card-body">
                    <p><?= htmlspecialchars($lot['description']) ?: 'Aucune description' ?></p>

                    <!-- Ajouter un consommable -->
                    <form method="POST" class="mt-3">
                        <input type="hidden" name="lot_id" value="<?= $lot['id'] ?>">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="nom" class="form-control" placeholder="Nom du Consommable" required>
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date_expiration" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="quantite" class="form-control" placeholder="Quantité" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="description" class="form-control" placeholder="Description (facultatif)">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" name="add_consumable" class="btn btn-primary">+</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Liste des consommables -->
                <div class="collapse" id="lot-<?= $lot['id'] ?>">
                    <div class="card-body">
                        <h5>Consommables</h5>
                        <?php
                        $stmt = $pdo->prepare("SELECT * FROM consommables WHERE lot_id = ?");
                        $stmt->execute([$lot['id']]);
                        $consommables = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <?php if (!empty($consommables)): ?>
                            <ul class="list-group">
                                <?php foreach ($consommables as $cons): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= htmlspecialchars($cons['nom']) ?></strong> 
                                            - <?= htmlspecialchars($cons['description']) ?> 
                                            - Quantité : <?= $cons['quantite'] ?> 
                                            - Expire le : <?= htmlspecialchars($cons['date_expiration']) ?>
                                        </div>
                                        <div>
                                            <!-- Boutons Modifier et Supprimer pour le consommable -->
                                            <a href="edit_consumable.php?id=<?= $cons['id'] ?>&lot_id=<?= $lot['id'] ?>&sac_id=<?= $sac_id ?>" class="btn btn-warning btn-sm me-2">Modifier</a>
                                            <a href="delete_consumable.php?id=<?= $cons['id'] ?>&lot_id=<?= $lot['id'] ?>&sac_id=<?= $sac_id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce consommable ?')">Supprimer</a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">Aucun consommable ajouté.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">Aucun lot ajouté.</p>
    <?php endif; ?>
</div>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000, // Durée de l'animation (en ms)
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
</body>
</html>
