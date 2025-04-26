<?php
session_start();
require_once __DIR__ . '/../../controllers/EventController.php';

// Check if an ID was provided
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = 'ID d\'événement invalide.';
    header('Location: /gestion_evenements/views/events/list_events.php');
    exit;
}

$event_id = (int)$_GET['id'];
$eventController = new EventController();
$eventData = $eventController->getEvent($event_id);

// Check if event exists
if ($eventData['status'] !== 'success' || !isset($eventData['event'])) {
    $_SESSION['error_message'] = 'Événement non trouvé.';
    header('Location: /gestion_evenements/views/events/list_events.php');
    exit;
}

$event = $eventData['event'];
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
    $date_evenement = isset($_POST['date_evenement']) ? trim($_POST['date_evenement']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    // Update event
    $result = $eventController->updateEvent($event_id, $titre, $date_evenement, $description);
    
    if ($result['status'] === 'success') {
        $_SESSION['success_message'] = $result['message'];
        header('Location: /gestion_evenements/views/events/list_events.php');
        exit;
    } else {
        $_SESSION['error_message'] = $result['message'];
        $errors = isset($result['errors']) ? $result['errors'] : [];
        
        // Keep the submitted values in case of error
        $event['titre'] = $titre;
        $event['date_evenement'] = $date_evenement;
        $event['description'] = $description;
    }
}

require_once __DIR__ . '/../layout/header.php';
?>

<h2>Modifier l'Événement</h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="edit_event.php?id=<?= $event_id ?>">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?= isset($errors['titre']) ? 'is-invalid' : '' ?>" id="titre" name="titre" value="<?= htmlspecialchars($event['titre']) ?>" required>
                <?php if (isset($errors['titre'])): ?>
                    <div class="invalid-feedback">
                        <?= $errors['titre'] ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="date_evenement" class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control <?= isset($errors['date_evenement']) ? 'is-invalid' : '' ?>" id="date_evenement" name="date_evenement" value="<?= htmlspecialchars($event['date_evenement']) ?>" required>
                <?php if (isset($errors['date_evenement'])): ?>
                    <div class="invalid-feedback">
                        <?= $errors['date_evenement'] ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" id="description" name="description" rows="5" required><?= htmlspecialchars($event['description']) ?></textarea>
                <?php if (isset($errors['description'])): ?>
                    <div class="invalid-feedback">
                        <?= $errors['description'] ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="list_events.php" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>