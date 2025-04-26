<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Inscription Model Class
 * Handles database operations for inscriptions
 */
class InscriptionModel {
    private $conn;
    private $table = "inscriptions";

    /**
     * Constructor that gets database connection
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create a new inscription
     * 
     * @param int $event_id The ID of the event
     * @param int $participant_id The ID of the participant
     * @return int|bool The ID of the newly created inscription or false on failure
     */
    public function create($event_id, $participant_id) {
        try {
            // Check if this participant is already registered for this event
            if ($this->inscriptionExists($event_id, $participant_id)) {
                return false;
            }
            
            $query = "INSERT INTO {$this->table} (event_id, participant_id, date_inscription) 
                      VALUES (:event_id, :participant_id, NOW())";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':event_id', $event_id);
            $stmt->bindParam(':participant_id', $participant_id);
            
            // Execute the query
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("InscriptionModel::create Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de l'inscription.");
        }
    }

    /**
     * Check if an inscription already exists
     * 
     * @param int $event_id The ID of the event
     * @param int $participant_id The ID of the participant
     * @return bool True if the inscription exists, false otherwise
     */
    public function inscriptionExists($event_id, $participant_id) {
        try {
            $query = "SELECT COUNT(*) as count FROM {$this->table} 
                      WHERE event_id = :event_id AND participant_id = :participant_id";
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':event_id', $event_id);
            $stmt->bindParam(':participant_id', $participant_id);
            
            // Execute the query
            $stmt->execute();
            $row = $stmt->fetch();
            
            return $row['count'] > 0;
        } catch (PDOException $e) {
            error_log("InscriptionModel::inscriptionExists Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la vérification de l'inscription.");
        }
    }

    /**
     * Read all inscriptions with event and participant details
     * 
     * @return array An array of inscriptions with event and participant details
     */
    public function readAll() {
        try {
            $query = "SELECT i.id, i.date_inscription, 
                      e.id as event_id, e.titre as event_titre, e.date_evenement, 
                      p.id as participant_id, p.nom as participant_nom, p.email 
                      FROM {$this->table} i 
                      JOIN events e ON i.event_id = e.id 
                      JOIN participants p ON i.participant_id = p.id 
                      ORDER BY i.date_inscription DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("InscriptionModel::readAll Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la récupération des inscriptions.");
        }
    }

    /**
     * Read inscriptions for a specific event
     * 
     * @param int $event_id The ID of the event
     * @return array An array of inscriptions for the specified event
     */
    public function readByEvent($event_id) {
        try {
            $query = "SELECT i.id, i.date_inscription, 
                      p.id as participant_id, p.nom as participant_nom, p.email 
                      FROM {$this->table} i 
                      JOIN participants p ON i.participant_id = p.id 
                      WHERE i.event_id = :event_id 
                      ORDER BY i.date_inscription DESC";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameter
            $stmt->bindParam(':event_id', $event_id);
            
            // Execute the query
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("InscriptionModel::readByEvent Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la récupération des inscriptions par événement.");
        }
    }

    /**
     * Read inscriptions for a specific participant
     * 
     * @param int $participant_id The ID of the participant
     * @return array An array of inscriptions for the specified participant
     */
    public function readByParticipant($participant_id) {
        try {
            $query = "SELECT i.id, i.date_inscription, 
                      e.id as event_id, e.titre as event_titre, e.date_evenement 
                      FROM {$this->table} i 
                      JOIN events e ON i.event_id = e.id 
                      WHERE i.participant_id = :participant_id 
                      ORDER BY i.date_inscription DESC";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameter
            $stmt->bindParam(':participant_id', $participant_id);
            
            // Execute the query
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("InscriptionModel::readByParticipant Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la récupération des inscriptions par participant.");
        }
    }

    /**
     * Delete an inscription
     * 
     * @param int $id The ID of the inscription to delete
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete($id) {
        try {
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            // Bind parameter
            $stmt->bindParam(':id', $id);
            
            // Execute the query
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("InscriptionModel::delete Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la suppression de l'inscription.");
        }
    }

    /**
     * Delete all inscriptions for a specific event
     * 
     * @param int $event_id The ID of the event
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteByEvent($event_id) {
        try {
            $query = "DELETE FROM {$this->table} WHERE event_id = :event_id";
            $stmt = $this->conn->prepare($query);
            
            // Bind parameter
            $stmt->bindParam(':event_id', $event_id);
            
            // Execute the query
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("InscriptionModel::deleteByEvent Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la suppression des inscriptions par événement.");
        }
    }

    /**
     * Delete all inscriptions for a specific participant
     * 
     * @param int $participant_id The ID of the participant
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteByParticipant($participant_id) {
        try {
            $query = "DELETE FROM {$this->table} WHERE participant_id = :participant_id";
            $stmt = $this->conn->prepare($query);
            
            // Bind parameter
            $stmt->bindParam(':participant_id', $participant_id);
            
            // Execute the query
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("InscriptionModel::deleteByParticipant Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la suppression des inscriptions par participant.");
        }
    }
}
?>
