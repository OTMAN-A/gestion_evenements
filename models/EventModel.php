<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Event Model Class
 * Handles database operations for events
 */
class EventModel {
    private $conn;
    private $table = "events";

    /**
     * Constructor that gets database connection
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create a new event
     * 
     * @param string $titre Title of the event
     * @param string $date_evenement Date of the event
     * @param string $description Description of the event
     * @return int|bool The ID of the newly created event or false on failure
     */
    public function create($titre, $date_evenement, $description) {
        try {
            $query = "INSERT INTO {$this->table} (titre, date_evenement, description) 
                      VALUES (:titre, :date_evenement, :description)";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':titre', $titre);
            $stmt->bindParam(':date_evenement', $date_evenement);
            $stmt->bindParam(':description', $description);
            
            // Execute the query
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("EventModel::create Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la création de l'événement.");
        }
    }

    /**
     * Read all events
     * 
     * @return array An array of events
     */
    public function readAll() {
        try {
            $query = "SELECT * FROM {$this->table} ORDER BY date_evenement DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("EventModel::readAll Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la récupération des événements.");
        }
    }

    /**
     * Read a single event by ID
     * 
     * @param int $id The ID of the event
     * @return array|false Event data or false if not found
     */
    public function readOne($id) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            // Bind parameter
            $stmt->bindParam(':id', $id);
            
            // Execute the query
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("EventModel::readOne Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la récupération de l'événement.");
        }
    }

    /**
     * Update an event
     * 
     * @param int $id The ID of the event
     * @param string $titre Title of the event
     * @param string $date_evenement Date of the event
     * @param string $description Description of the event
     * @return bool True if update was successful, false otherwise
     */
    public function update($id, $titre, $date_evenement, $description) {
        try {
            $query = "UPDATE {$this->table} 
                      SET titre = :titre, date_evenement = :date_evenement, description = :description 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':titre', $titre);
            $stmt->bindParam(':date_evenement', $date_evenement);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':id', $id);
            
            // Execute the query
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("EventModel::update Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la mise à jour de l'événement.");
        }
    }

    /**
     * Delete an event
     * 
     * @param int $id The ID of the event to delete
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
            error_log("EventModel::delete Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la suppression de l'événement.");
        }
    }
}
?>
