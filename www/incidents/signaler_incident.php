<?php
include '../includes/db.php';

// Récupérer l'ID du sac depuis l'URL ou un paramètre (facultatif)
$sac_id = $_GET['sac_id'] ?? null;

// Récupérer tous les sacs pour la liste déroulante
$stmt = $pdo->query("SELECT id, nom FROM sacs_medicaux");
$sacs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si un incident est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type_incident = $_POST['type_incident'];
    $reference_id = $_POST['reference_id'];
    $description = $_POST['description'];

    // Enregistrer l'incident dans la base de données
    $stmt = $pdo->prepare("INSERT INTO incidents (type_incident, reference_id, description, statut, utilisateur_id) VALUES (?, ?, ?, 'Non Résolu', ?)");
    $stmt->execute([$type_incident, $reference_id, $description, $_SESSION['user_id']]);

    // Redirection après soumission
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Signaler un Incident</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"> <!-- Animate.css -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css"> <!-- AOS -->
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
    <h1>Signaler un Incident</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="type_incident" class="form-label">Type d'Incident</label>
            <select class="form-control" id="type_incident" name="type_incident" required>
                <option value="Sac">Sac</option>
                <option value="Médicament">Médicament</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="reference_id" class="form-label">Référence (Sélectionnez un sac)</label>
            <select class="form-control" id="reference_id" name="reference_id" required>
                <option value="">-- Sélectionnez un sac --</option>
                <?php foreach ($sacs as $sac): ?>
                    <option value="<?= htmlspecialchars($sac['id']) ?>"
                        <?= $sac_id == $sac['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sac['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description du problème</label>
            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Soumettre</button>
        <a href="../login.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000 });
</script>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
</body>
</html>
