<?php
session_start();
require_once __DIR__ . '/layout/header.php';
?>

<div class="jumbotron bg-light p-5 rounded">
    <h1 class="display-4">Bienvenue sur le système de gestion des événements</h1>
    <p class="lead">Cette application vous permet de gérer des événements et les inscriptions des participants.</p>
    <hr class="my-4">
    <p>Utilisez les différentes fonctionnalités pour créer des événements, enregistrer des participants et gérer les inscriptions.</p>
    <div class="d-flex flex-wrap gap-2 mt-4">
        <a class="btn btn-primary btn-lg" href="/gestion_evenements/views/events/create_event.php" role="button">Créer un événement</a>
        <a class="btn btn-success btn-lg" href="/gestion_evenements/views/participants/register_participant.php" role="button">Inscrire un participant</a>
        <a class="btn btn-info btn-lg" href="/gestion_evenements/views/events/list_events.php" role="button">Voir les événements</a>
        <a class="btn btn-secondary btn-lg" href="/gestion_evenements/views/inscriptions/list_inscriptions.php" role="button">Voir les inscriptions</a>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Gestion des événements</h5>
                <p class="card-text">Créez, modifiez et supprimez des événements selon vos besoins.</p>
                <a href="/gestion_evenements/views/events/list_events.php" class="btn btn-outline-primary">Gérer les événements</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Inscription des participants</h5>
                <p class="card-text">Enregistrez des participants et inscrivez-les à des événements.</p>
                <a href="/gestion_evenements/views/participants/register_participant.php" class="btn btn-outline-success">Inscrire un participant</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Suivi des inscriptions</h5>
                <p class="card-text">Consultez la liste des inscriptions par événement ou par participant.</p>
                <a href="/gestion_evenements/views/inscriptions/list_inscriptions.php" class="btn btn-outline-info">Voir les inscriptions</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/layout/footer.php';
?>
