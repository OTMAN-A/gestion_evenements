<?php
require_once __DIR__ . '/../models/InscriptionModel.php';
require_once __DIR__ . '/../models/EventModel.php';
require_once __DIR__ . '/../models/ParticipantModel.php';

/**
 * Inscription Controller Class
 * Implements business logic for inscriptions
 */
class InscriptionController {
    private $inscriptionModel;
    private $eventModel;
    private $participantModel;

    /**
     * Constructor that initializes models
     */
    public function __construct() {
        $this->inscriptionModel = new InscriptionModel();
        $this->eventModel = new EventModel();
        $this->participantModel = new ParticipantModel();
    }

    /**
     * Create a new inscription
     * 
     * @param int $event_id The ID of the event
     * @param int $participant_id The ID of the participant
     * @return array Result information with status and message
     */
    public function createInscription($event_id, $participant_id) {
        try {
            // Validate event and participant IDs
            $errors = $this->validateInscriptionData($event_id, $participant_id);
            
            // If there are validation errors, return them
            if (!empty($errors)) {
                return [
                    'status' => 'error',
                    'message' => 'Validation errors:',
                    'errors' => $errors
                ];
            }

            // Check if the inscription already exists
            if ($this->inscriptionModel->inscriptionExists($event_id, $participant_id)) {
                return [
                    'status' => 'error',
                    'message' => 'Ce participant est déjà inscrit à cet événement.'
                ];
            }

            // Create the inscription
            $inscription_id = $this->inscriptionModel->create($event_id, $participant_id);
            
            if ($inscription_id) {
                return [
                    'status' => 'success',
                    'message' => 'Inscription créée avec succès.',
                    'inscription_id' => $inscription_id
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Impossible de créer l\'inscription.'
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all inscriptions
     * 
     * @return array All inscriptions or error information
     */
    public function getAllInscriptions() {
        try {
            $inscriptions = $this->inscriptionModel->readAll();
            return [
                'status' => 'success',
                'inscriptions' => $inscriptions
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get inscriptions for a specific event
     * 
     * @param int $event_id The ID of the event
     * @return array Inscriptions for the event or error information
     */
    public function getInscriptionsByEvent($event_id) {
        try {
            // Check if the event exists
            $event = $this->eventModel->readOne($event_id);
            if (!$event) {
                return [
                    'status' => 'error',
                    'message' => 'Événement non trouvé.'
                ];
            }

            $inscriptions = $this->inscriptionModel->readByEvent($event_id);
            return [
                'status' => 'success',
                'event' => $event,
                'inscriptions' => $inscriptions
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get inscriptions for a specific participant
     * 
     * @param int $participant_id The ID of the participant
     * @return array Inscriptions for the participant or error information
     */
    public function getInscriptionsByParticipant($participant_id) {
        try {
            // Check if the participant exists
            $participant = $this->participantModel->readOne($participant_id);
            if (!$participant) {
                return [
                    'status' => 'error',
                    'message' => 'Participant non trouvé.'
                ];
            }

            $inscriptions = $this->inscriptionModel->readByParticipant($participant_id);
            return [
                'status' => 'success',
                'participant' => $participant,
                'inscriptions' => $inscriptions
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete an inscription
     * 
     * @param int $id The ID of the inscription to delete
     * @return array Result information with status and message
     */
    public function deleteInscription($id) {
        try {
            // Delete the inscription
            $result = $this->inscriptionModel->delete($id);
            
            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Inscription supprimée avec succès.'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Impossible de supprimer l\'inscription.'
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate inscription data
     * 
     * @param int $event_id The ID of the event
     * @param int $participant_id The ID of the participant
     * @return array Array of validation errors, empty if no errors
     */
    private function validateInscriptionData($event_id, $participant_id) {
        $errors = [];
        
        // Validate event_id
        if (empty($event_id) || !is_numeric($event_id)) {
            $errors['event_id'] = 'ID d\'événement invalide.';
        } else {
            // Check if the event exists
            $event = $this->eventModel->readOne($event_id);
            if (!$event) {
                $errors['event_id'] = 'Événement non trouvé.';
            }
        }
        
        // Validate participant_id
        if (empty($participant_id) || !is_numeric($participant_id)) {
            $errors['participant_id'] = 'ID de participant invalide.';
        } else {
            // Check if the participant exists
            $participant = $this->participantModel->readOne($participant_id);
            if (!$participant) {
                $errors['participant_id'] = 'Participant non trouvé.';
            }
        }
        
        return $errors;
    }
}
?>
