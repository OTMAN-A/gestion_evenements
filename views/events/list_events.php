<?php
session_start();
require_once __DIR__ . '/../../controllers/EventController.php';
require_once __DIR__ . '/../../controllers/InscriptionController.php';

// Handle event deletion
if (isset($_POST['delete_event']) && isset($_POST['event_id'])) {
    $event_id = (int)$_POST['event_id'];
    $eventController = new EventController();
    $result = $eventController->deleteEvent($event_id);
    
    if ($result['status'] === 'success') {
        $_SESSION['success_message'] = $result['message'];
    } else {
        $_SESSION['error_message'] = $result['message'];
    }
    
    // Redirect to avoid resubmission
    header('Location: /gestion_evenements/views/events/list_events.php');
    exit;
}

// Get all events
$eventController = new EventController();
$result = $eventController->getAllEvents();

// Check for errors
if ($result['status'] !== 'success') {
    $_SESSION['error_message'] = $result['message'];
    $events = [];
} else {
    $events = $result['events'];
}

require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Liste des Événements</h2>
    <!-- <a href="views/events/create_event.php" class="btn btn-primary"> -->
        <a href="/gestion_evenements/views/events/create_event.php" class="btn btn-primary">

        <i class="fas fa-plus"></i> Créer un événement
    </a>
</div>

<?php if (empty($events)): ?>
    <div class="alert alert-info">
        Aucun événement n'a été trouvé. Cliquez sur "Créer un événement" pour en ajouter un nouveau.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['id']) ?></td>
                        <td><?= htmlspecialchars($event['titre']) ?></td>
                        <td><?= date('d/m/Y', strtotime($event['date_evenement'])) ?></td>
                        <td>
                            <?php 
                            $description = htmlspecialchars($event['description']);
                            echo strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                            ?>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="/gestion_evenements/views/events/edit_event.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $event['id'] ?>" title="Supprimer">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                            
                            <!-- Delete Confirmation Modal -->
                            <div class="modal fade" id="deleteModal<?= $event['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $event['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel<?= $event['id'] ?>">Confirmer la suppression</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Êtes-vous sûr de vouloir supprimer l'événement "<?= htmlspecialchars($event['titre']) ?>" ?
                                            <p class="text-danger mt-2">
                                                <strong>Attention :</strong> Cette action supprimera également toutes les inscriptions associées à cet événement.
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <form method="POST" action="">
                                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                                <button type="submit" name="delete_event" class="btn btn-danger">Supprimer</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>
