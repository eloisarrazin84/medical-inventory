<nav class="navbar navbar-expand-lg navbar-light bg-transparent">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="#">
            <img src="https://outdoorsecours.fr/wp-content/uploads/2023/07/thumbnail_image001-1.png" alt="Logo" height="50">
        </a>
        <!-- Bouton pour mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Liens -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="btn btn-outline-primary mx-2 nav-link" href="dashboard.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Accéder au tableau de bord">
                        <i class="fas fa-home"></i> Tableau de Bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-warning mx-2 nav-link" href="../medicaments/index.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Gérer les médicaments">
                        <i class="fas fa-pills"></i> Gestion des Médicaments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-danger mx-2 nav-link" href="../users/manage_users.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Gérer les utilisateurs">
                        <i class="fas fa-users"></i> Gestion des Utilisateurs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-dark mx-2 nav-link" href="logout.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Se déconnecter">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
