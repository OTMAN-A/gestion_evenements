<?php
session_start();
require_once __DIR__ . '/../../controllers/EventController.php';

// Initialize variables
$titre = '';
$date_evenement = '';
$description = '';
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
    $date_evenement = isset($_POST['date_evenement']) ? trim($_POST['date_evenement']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    // Create event
    $eventController = new EventController();
    $result = $eventController->createEvent($titre, $date_evenement, $description);
    
    if ($result['status'] === 'success') {
        $_SESSION['success_message'] = $result['message'];
        header('Location: /gestion_evenements/views/events/list_events.php');
        exit;
    } else {
        $_SESSION['error_message'] = $result['message'];
        $errors = isset($result['errors']) ? $result['errors'] : [];
    }
}

require_once __DIR__ . '/../layout/header.php';
?>

<h2>Créer un Événement</h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?= isset($errors['titre']) ? 'is-invalid' : '' ?>" id="titre" name="titre" value="<?= htmlspecialchars($titre) ?>" required>
                <?php if (isset($errors['titre'])): ?>
                    <div class="invalid-feedback">
                        <?= $errors['titre'] ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="date_evenement" class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control <?= isset($errors['date_evenement']) ? 'is-invalid' : '' ?>" id="date_evenement" name="date_evenement" value="<?= htmlspecialchars($date_evenement) ?>" required>
                <?php if (isset($errors['date_evenement'])): ?>
                    <div class="invalid-feedback">
                        <?= $errors['date_evenement'] ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" id="description" name="description" rows="5" required><?= htmlspecialchars($description) ?></textarea>
                <?php if (isset($errors['description'])): ?>
                    <div class="invalid-feedback">
                        <?= $errors['description'] ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="/gestion_evenements/views/events/list_events.php" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Créer l'événement</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>
