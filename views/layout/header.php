<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Événements</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <header class="bg-dark text-white mb-4">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center py-3">
                <div>
                    <h1 class="h3">Gestion des Événements</h1>
                </div>
                <nav>
                    <ul class="nav">
                        <li class="nav-item"><a href="/gestion_evenements/views/index.php" class="nav-link px-2 text-white">Accueil</a></li>
                        <li class="nav-item"><a href="/gestion_evenements/views/events/list_events.php" class="nav-link px-2 text-white">Événements</a></li>
                        <li class="nav-item"><a href="/gestion_evenements/views/events/create_event.php" class="nav-link px-2 text-white">Créer un événement</a></li>
                        <li class="nav-item"><a href="/gestion_evenements/views/participants/register_participant.php" class="nav-link px-2 text-white">Inscription</a></li>
                        <li class="nav-item"><a href="/gestion_evenements/views/inscriptions/list_inscriptions.php" class="nav-link px-2 text-white">Liste des inscriptions</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main class="container mb-4">
        <?php
        // Display success message if set
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo $_SESSION['success_message'];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['success_message']);
        }
        
        // Display error message if set
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            echo $_SESSION['error_message'];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['error_message']);
        }
        ?>
