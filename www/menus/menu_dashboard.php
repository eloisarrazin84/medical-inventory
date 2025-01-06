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
                    <a class="btn btn-outline-primary mx-2 nav-link" href="dashboard.php">
                        <i class="fas fa-home"></i> Tableau de Bord
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <button class="btn btn-outline-secondary mx-2 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-cogs"></i> Gestion
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="sacs/index.php"><i class="fas fa-briefcase-medical"></i> Gestion des Sacs</a></li>
                        <li><a class="dropdown-item" href="lieux/gestion_lieux.php"><i class="fas fa-map-marker-alt"></i> Gestion des Lieux</a></li>
                        <li><a class="dropdown-item" href="medicaments/choisir_sac.php"><i class="fas fa-pills"></i> Gestion des Médicaments</a></li>
                        <li><a class="dropdown-item" href="incidents/incidents.php"><i class="fas fa-exclamation-triangle"></i> Gestion des Incidents</a></li>
                        <li><a class="dropdown-item" href="rapports/afficher_rapports.php"><i class="fas fa-report"></i> Gestion des Rapports</a></li>
                        <li><a class="dropdown-item" href="users/manage_users.php"><i class="fas fa-users"></i> Gestion des Utilisateurs</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-dark mx-2 nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
