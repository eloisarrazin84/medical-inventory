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
                <!-- Tableau de Bord -->
                <li class="nav-item">
                    <a class="btn btn-outline-primary mx-2 nav-link" href="dashboard.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Accéder au tableau de bord">
                        <i class="fas fa-home"></i> Tableau de Bord
                    </a>
                </li>
                <!-- Bouton Gestion avec menu déroulant -->
                <li class="nav-item dropdown">
                    <a class="btn btn-outline-secondary mx-2 nav-link dropdown-toggle" href="#" id="gestionDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-cogs"></i> Gestion
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="gestionDropdown">
                        <li>
                            <a class="dropdown-item" href="sacs/index.php">
                                <i class="fas fa-briefcase-medical"></i> Gestion des Sacs
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="lieux/gestion_lieux.php">
                                <i class="fas fa-archive"></i> Gestion des Lieux
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="medicaments/choisir_sac.php">
                                <i class="fas fa-pills"></i> Gestion des Médicaments
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="users/manage_users.php">
                                <i class="fas fa-users"></i> Gestion des Utilisateurs
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Déconnexion -->
                <li class="nav-item">
                    <a class="btn btn-outline-dark mx-2 nav-link" href="logout.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Se déconnecter">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Ajout des scripts nécessaires -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialisation des tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Forcer le fonctionnement des menus déroulants si nécessaire
    var dropdownTriggerList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownTriggerList.map(function (dropdownTriggerEl) {
        return new bootstrap.Dropdown(dropdownTriggerEl);
    });
</script>
