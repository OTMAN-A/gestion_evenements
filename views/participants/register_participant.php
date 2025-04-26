<?php
session_start();
require_once __DIR__ . '/../../controllers/ParticipantController.php';
require_once __DIR__ . '/../../controllers/EventController.php';
require_once __DIR__ . '/../../controllers/InscriptionController.php';

// Initialize variables
$nom = '';
$email = '';
$event_id = '';
$errors = [];

// Get all events for the dropdown
$eventController = new EventController();
$eventsResult = $eventController->getAllEvents();

if ($eventsResult['status'] !== 'success' || empty($eventsResult['events'])) {
    $_SESSION['error_message'] = 'Aucun événement disponible. Veuillez créer un événement d\'abord.';
    header('Location: /gestion_evenements/views/events/create_event.php');
    exit;
}

$events = $eventsResult['events'];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : '';
    
    // Create or get participant
    $participantController = new ParticipantController();
    $participantResult = $participantController->createParticipant($nom, $email);
    
    if ($participantResult['status'] === 'success') {
        $participant_id = $participantResult['participant_id'];
        
        // Register the participant to the selected event
        $inscriptionController = new InscriptionController();
        $inscriptionResult = $inscriptionController->createInscription($event_id, $participant_id);
        
        if ($inscriptionResult['status'] === 'success') {
            $_SESSION['success_message'] = 'Participant inscrit avec succès.';
            header('Location:/gestion_evenements/views/inscriptions/list_inscriptions.php');
            exit;
        } else {
            $_SESSION['error_message'] = $inscriptionResult['message'];
            $errors = isset($inscriptionResult['errors']) ? $inscriptionResult['errors'] : [];
        }
    } else {
        $_SESSION['error_message'] = $participantResult['message'];
        $errors = isset($participantResult['errors']) ? $participantResult['errors'] : [];
    }
}

require_once __DIR__ . '/../layout/header.php';
?>

<h2>Inscription à un Événement</h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" required>
                <?php if (isset($errors['nom'])): ?>
                    <div class="invalid-feedback">
                        <?= $errors['nom'] ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback">
                        <?= $errors['email'] ?>
                    </div>
                <?php endif; ?>
                <div class="form-text">Si l'email existe déjà, le participant sera associé à l'événement sélectionné.</div>
            </div>
            
            <div class="mb-3">
                <label for="event_id" class="form-label">Événement <span class="text-danger">*</span></label>
                <select class="form-select <?= isset($errors['event_id']) ? 'is-invalid' : '' ?>" id="event_id" name="event_id" required>
                    <option value="">Sélectionnez un événement</option>
                    <?php foreach ($events as $event): ?>
                        <option value="<?= $event['id'] ?>" <?= $event_id == $event['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($event['titre']) ?> - <?= date('d/m/Y', strtotime($event['date_evenement'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['event_id'])): ?>
                    <div class="invalid-feedback">
                        <?= $errors['event_id'] ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="/gestion_evenements/views/index.php" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-success">Inscrire à l'événement</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>
