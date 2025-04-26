<?php
require_once __DIR__ . '/../models/ParticipantModel.php';
require_once __DIR__ . '/../models/InscriptionModel.php';

/**
 * Participant Controller Class
 * Implements business logic for participants
 */
class ParticipantController {
    private $participantModel;
    private $inscriptionModel;

    /**
     * Constructor that initializes models
     */
    public function __construct() {
        $this->participantModel = new ParticipantModel();
        $this->inscriptionModel = new InscriptionModel();
    }

    /**
     * Create a new participant
     * 
     * @param string $nom Name of the participant
     * @param string $email Email of the participant
     * @return array Result information with status and message
     */
    public function createParticipant($nom, $email) {
        try {
            // Validate input data
            $errors = $this->validateParticipantData($nom, $email);
            
            // If there are validation errors, return them
            if (!empty($errors)) {
                return [
                    'status' => 'error',
                    'message' => 'Validation errors:',
                    'errors' => $errors
                ];
            }

            // Create the participant
            $participant_id = $this->participantModel->create($nom, $email);
            
            if ($participant_id) {
                return [
                    'status' => 'success',
                    'message' => 'Participant créé avec succès.',
                    'participant_id' => $participant_id
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Impossible de créer le participant.'
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
     * Get all participants
     * 
     * @return array All participants or error information
     */
    public function getAllParticipants() {
        try {
            $participants = $this->participantModel->readAll();
            return [
                'status' => 'success',
                'participants' => $participants
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get a single participant by ID
     * 
     * @param int $id The ID of the participant
     * @return array Participant data or error information
     */
    public function getParticipant($id) {
        try {
            $participant = $this->participantModel->readOne($id);
            
            if ($participant) {
                return [
                    'status' => 'success',
                    'participant' => $participant
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Participant non trouvé.'
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
     * Update a participant
     * 
     * @param int $id The ID of the participant
     * @param string $nom Name of the participant
     * @param string $email Email of the participant
     * @return array Result information with status and message
     */
    public function updateParticipant($id, $nom, $email) {
        try {
            // Validate input data
            $errors = $this->validateParticipantData($nom, $email);
            
            // If there are validation errors, return them
            if (!empty($errors)) {
                return [
                    'status' => 'error',
                    'message' => 'Validation errors:',
                    'errors' => $errors
                ];
            }

            // Check if the participant exists
            $participant = $this->participantModel->readOne($id);
            if (!$participant) {
                return [
                    'status' => 'error',
                    'message' => 'Participant non trouvé.'
                ];
            }

            // Check if email is already in use by another participant
            if ($email !== $participant['email'] && $this->participantModel->emailExists($email)) {
                return [
                    'status' => 'error',
                    'message' => 'Cet email est déjà utilisé par un autre participant.'
                ];
            }

            // Update the participant
            $result = $this->participantModel->update($id, $nom, $email);
            
            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Participant mis à jour avec succès.'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Impossible de mettre à jour le participant.'
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
     * Delete a participant
     * 
     * @param int $id The ID of the participant to delete
     * @return array Result information with status and message
     */
    public function deleteParticipant($id) {
        try {
            // Check if the participant exists
            $participant = $this->participantModel->readOne($id);
            if (!$participant) {
                return [
                    'status' => 'error',
                    'message' => 'Participant non trouvé.'
                ];
            }

            // First delete all inscriptions for this participant
            $this->inscriptionModel->deleteByParticipant($id);
            
            // Then delete the participant
            $result = $this->participantModel->delete($id);
            
            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Participant et inscriptions associées supprimés avec succès.'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Impossible de supprimer le participant.'
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
     * Validate participant data
     * 
     * @param string $nom Name of the participant
     * @param string $email Email of the participant
     * @return array Array of validation errors, empty if no errors
     */
    private function validateParticipantData($nom, $email) {
        $errors = [];
        
        // Validate name
        if (empty($nom)) {
            $errors['nom'] = 'Le nom est obligatoire.';
        } elseif (strlen($nom) > 255) {
            $errors['nom'] = 'Le nom ne doit pas dépasser 255 caractères.';
        }
        
        // Validate email
        if (empty($email)) {
            $errors['email'] = 'L\'email est obligatoire.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format d\'email invalide.';
        } elseif (strlen($email) > 255) {
            $errors['email'] = 'L\'email ne doit pas dépasser 255 caractères.';
        }
        
        return $errors;
    }
}
?>
