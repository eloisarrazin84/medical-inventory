<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <img src="https://outdoorsecours.fr/wp-content/uploads/2023/07/thumbnail_image001-1.png" alt="Logo" height="50" class="me-2">
            <span class="fw-bold text-dark">Outdoor Secours</span>
        </a>

        <!-- Bouton Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu principal -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                
                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell fa-lg"></i>
                        <span id="notif-badge" class="badge rounded-pill bg-danger" style="display: none;">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-2" id="notificationsList">
                        <li class="dropdown-header fw-bold">🔔 Notifications</li>
                        <li id="notif-container">
                            <p class="text-center text-muted">Aucune nouvelle notification</p>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item text-center text-primary" onclick="markAllAsRead()">Tout marquer comme lu</button></li>
                    </ul>
                </li>

                <!-- Menu Gestion -->
                <li class="nav-item dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle mx-2 fw-bold" id="dropdownGestion" data-bs-toggle="dropdown">
                        <i class="fas fa-cogs"></i> Gestion
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="sacs/index.php"><i class="fas fa-briefcase-medical"></i> Sacs Médicaux</a></li>
                        <li><a class="dropdown-item" href="lieux/gestion_lieux.php"><i class="fas fa-map-marker-alt"></i> Lieux de Stockage</a></li>
                        <li><a class="dropdown-item" href="medicaments/choisir_sac.php"><i class="fas fa-pills"></i> Médicaments</a></li>
                        <li><a class="dropdown-item" href="incidents/incidents.php"><i class="fas fa-exclamation-triangle"></i> Incidents</a></li>
                        <li><a class="dropdown-item" href="rapports/afficher_rapports.php"><i class="fas fa-file-alt"></i> Rapports</a></li>
                        <li><a class="dropdown-item" href="users/manage_users.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
                    </ul>
                </li>
                <!-- Carte -->
                 <li class="nav-item">
                <a href="carte.php" class="btn btn-primary">
    <i class="fas fa-map-marked-alt"></i> Voir la Carte
</a>
</li>               
                <!-- Déconnexion -->
                <li class="nav-item">
                    <a class="btn btn-outline-danger fw-bold" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
