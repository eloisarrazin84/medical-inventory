<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <img src="https://outdoorsecours.fr/wp-content/uploads/2023/07/thumbnail_image001-1.png" alt="Logo" height="50" class="me-2">
            <span class="fw-bold">Outdoor Secours</span>
        </a>
        <!-- Bouton Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Menu -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light mx-2" href="dashboard.php">
                        <i class="fas fa-home"></i> Tableau de Bord
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <button class="btn btn-outline-success dropdown-toggle mx-2" type="button" id="dropdownGestion" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-cogs"></i> Gestion
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownGestion">
                        <li><a class="dropdown-item" href="sacs/index.php"><i class="fas fa-briefcase-medical"></i> Sacs Médicaux</a></li>
                        <li><a class="dropdown-item" href="lieux/gestion_lieux.php"><i class="fas fa-map-marker-alt"></i> Lieux de Stockage</a></li>
                        <li><a class="dropdown-item" href="medicaments/choisir_sac.php"><i class="fas fa-pills"></i> Médicaments</a></li>
                        <li><a class="dropdown-item" href="incidents/incidents.php"><i class="fas fa-exclamation-triangle"></i> Incidents</a></li>
                        <li><a class="dropdown-item" href="rapports/afficher_rapports.php"><i class="fas fa-file-alt"></i> Rapports</a></li>
                        <li><a class="dropdown-item" href="users/manage_users.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-danger mx-2" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
