<?php
session_start();
require_once __DIR__ . '/../../controllers/InscriptionController.php';

// Handle inscription deletion
if (isset($_POST['delete_inscription']) && isset($_POST['inscription_id'])) {
    $inscription_id = (int)$_POST['inscription_id'];
    $inscriptionController = new InscriptionController();
    $result = $inscriptionController->deleteInscription($inscription_id);
    
    if ($result['status'] === 'success') {
        $_SESSION['success_message'] = $result['message'];
    } else {
        $_SESSION['error_message'] = $result['message'];
    }
    
    // Redirect to avoid resubmission
    header('Location: /gestion_evenements/views/inscriptions/list_inscriptions.php');
    exit;
}

// Get all inscriptions
$inscriptionController = new InscriptionController();
$result = $inscriptionController->getAllInscriptions();

// Check for errors
if ($result['status'] !== 'success') {
    $_SESSION['error_message'] = $result['message'];
    $inscriptions = [];
} else {
    $inscriptions = $result['inscriptions'];
}

require_once __DIR__ . '/../layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Liste des Inscriptions</h2>
    <a href="/gestion_evenements/views/participants/register_participant.php" class="btn btn-success">
        <i class="fas fa-plus"></i> Nouvelle inscription
    </a>
</div>

<?php if (empty($inscriptions)): ?>
    <div class="alert alert-info">
        Aucune inscription n'a été trouvée. Cliquez sur "Nouvelle inscription" pour ajouter une inscription.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Événement</th>
                    <th>Date de l'événement</th>
                    <th>Participant</th>
                    <th>Email</th>
                    <th>Date d'inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inscriptions as $inscription): ?>
                    <tr>
                        <td><?= htmlspecialchars($inscription['id']) ?></td>
                        <td><?= htmlspecialchars($inscription['event_titre']) ?></td>
                        <td><?= date('d/m/Y', strtotime($inscription['date_evenement'])) ?></td>
                        <td><?= htmlspecialchars($inscription['participant_nom']) ?></td>
                        <td><?= htmlspecialchars($inscription['email']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($inscription['date_inscription'])) ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $inscription['id'] ?>" title="Supprimer">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                            
                            <!-- Delete Confirmation Modal -->
                            <div class="modal fade" id="deleteModal<?= $inscription['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $inscription['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel<?= $inscription['id'] ?>">Confirmer la suppression</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Êtes-vous sûr de vouloir supprimer l'inscription de 
                                            <strong><?= htmlspecialchars($inscription['participant_nom']) ?></strong> 
                                            à l'événement 
                                            <strong><?= htmlspecialchars($inscription['event_titre']) ?></strong> ?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <form method="POST" action="">
                                                <input type="hidden" name="inscription_id" value="<?= $inscription['id'] ?>">
                                                <button type="submit" name="delete_inscription" class="btn btn-danger">Supprimer</button>
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
