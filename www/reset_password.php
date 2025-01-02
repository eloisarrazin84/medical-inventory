<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Vérifier si le token est valide
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ?");
    $stmt->execute([$token]);
    $reset_request = $stmt->fetch();

    if ($reset_request) {
        // Mettre à jour le mot de passe de l'utilisateur
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$password, $reset_request['email']]);

        // Supprimer le token après utilisation
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);

        $success = "Votre mot de passe a été réinitialisé avec succès.";
    } else {
        $error = "Lien de réinitialisation invalide ou expiré.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Réinitialisation du Mot de Passe</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Réinitialisation du Mot de Passe</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
        <div class="mb-3">
            <label for="password" class="form-label">Nouveau Mot de Passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Réinitialiser</button>
    </form>
</div>
</body>
</html>
