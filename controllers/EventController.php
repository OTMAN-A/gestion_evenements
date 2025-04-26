<?php
require_once __DIR__ . '/../models/EventModel.php';
require_once __DIR__ . '/../models/InscriptionModel.php';

/**
 * Event Controller Class
 * Implements business logic for events
 */
class EventController {
    private $eventModel;
    private $inscriptionModel;

    /**
     * Constructor that initializes models
     */
    public function __construct() {
        $this->eventModel = new EventModel();
        $this->inscriptionModel = new InscriptionModel();
    }

    /**
     * Create a new event
     * 
     * @param string $titre Title of the event
     * @param string $date_evenement Date of the event
     * @param string $description Description of the event
     * @return array Result information with status and message
     */
    public function createEvent($titre, $date_evenement, $description) {
        try {
            // Validate input data
            $errors = $this->validateEventData($titre, $date_evenement, $description);
            
            // If there are validation errors, return them
            if (!empty($errors)) {
                return [
                    'status' => 'error',
                    'message' => 'Validation errors:',
                    'errors' => $errors
                ];
            }

            // Create the event
            $event_id = $this->eventModel->create($titre, $date_evenement, $description);
            
            if ($event_id) {
                return [
                    'status' => 'success',
                    'message' => 'Événement créé avec succès.',
                    'event_id' => $event_id
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Impossible de créer l\'événement.'
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
     * Get all events
     * 
     * @return array All events or error information
     */
    public function getAllEvents() {
        try {
            $events = $this->eventModel->readAll();
            return [
                'status' => 'success',
                'events' => $events
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get a single event by ID
     * 
     * @param int $id The ID of the event
     * @return array Event data or error information
     */
    public function getEvent($id) {
        try {
            $event = $this->eventModel->readOne($id);
            
            if ($event) {
                return [
                    'status' => 'success',
                    'event' => $event
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Événement non trouvé.'
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
     * Update an event
     * 
     * @param int $id The ID of the event
     * @param string $titre Title of the event
     * @param string $date_evenement Date of the event
     * @param string $description Description of the event
     * @return array Result information with status and message
     */
    public function updateEvent($id, $titre, $date_evenement, $description) {
        try {
            // Validate input data
            $errors = $this->validateEventData($titre, $date_evenement, $description);
            
            // If there are validation errors, return them
            if (!empty($errors)) {
                return [
                    'status' => 'error',
                    'message' => 'Validation errors:',
                    'errors' => $errors
                ];
            }

            // Check if the event exists
            $event = $this->eventModel->readOne($id);
            if (!$event) {
                return [
                    'status' => 'error',
                    'message' => 'Événement non trouvé.'
                ];
            }

            // Update the event
            $result = $this->eventModel->update($id, $titre, $date_evenement, $description);
            
            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Événement mis à jour avec succès.'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Impossible de mettre à jour l\'événement.'
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
     * Delete an event
     * 
     * @param int $id The ID of the event to delete
     * @return array Result information with status and message
     */
    public function deleteEvent($id) {
        try {
            // Check if the event exists
            $event = $this->eventModel->readOne($id);
            if (!$event) {
                return [
                    'status' => 'error',
                    'message' => 'Événement non trouvé.'
                ];
            }

            // First delete all inscriptions for this event
            $this->inscriptionModel->deleteByEvent($id);
            
            // Then delete the event
            $result = $this->eventModel->delete($id);
            
            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Événement et inscriptions associées supprimés avec succès.'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Impossible de supprimer l\'événement.'
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
     * Validate event data
     * 
     * @param string $titre Title of the event
     * @param string $date_evenement Date of the event
     * @param string $description Description of the event
     * @return array Array of validation errors, empty if no errors
     */
    private function validateEventData($titre, $date_evenement, $description) {
        $errors = [];
        
        // Validate title
        if (empty($titre)) {
            $errors['titre'] = 'Le titre est obligatoire.';
        } elseif (strlen($titre) > 255) {
            $errors['titre'] = 'Le titre ne doit pas dépasser 255 caractères.';
        }
        
        // Validate date
        if (empty($date_evenement)) {
            $errors['date_evenement'] = 'La date est obligatoire.';
        } else {
            $date = date_create_from_format('Y-m-d', $date_evenement);
            if (!$date || date_format($date, 'Y-m-d') !== $date_evenement) {
                $errors['date_evenement'] = 'Format de date invalide. Utilisez le format YYYY-MM-DD.';
            } elseif ($date < date_create('today')) {
                $errors['date_evenement'] = 'La date ne peut pas être dans le passé.';
            }
        }
        
        // Validate description
        if (empty($description)) {
            $errors['description'] = 'La description est obligatoire.';
        }
        
        return $errors;
    }
}
?>
