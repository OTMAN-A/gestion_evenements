<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Participant Model Class
 * Handles database operations for participants
 */
class ParticipantModel {
    private $conn;
    private $table = "participants";

    /**
     * Constructor that gets database connection
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create a new participant
     * 
     * @param string $nom Name of the participant
     * @param string $email Email of the participant
     * @return int|bool The ID of the newly created participant or false on failure
     */
    public function create($nom, $email) {
        try {
            // First check if the email is already registered
            if ($this->emailExists($email)) {
                // Get the participant id for this email
                return $this->getParticipantIdByEmail($email);
            }
            
            $query = "INSERT INTO {$this->table} (nom, email) VALUES (:nom, :email)";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':email', $email);
            
            // Execute the query
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("ParticipantModel::create Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la création du participant.");
        }
    }

    /**
     * Check if an email already exists
     * 
     * @param string $email Email to check
     * @return bool True if the email exists, false otherwise
     */
    public function emailExists($email) {
        try {
            $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            
            // Bind parameter
            $stmt->bindParam(':email', $email);
            
            // Execute the query
            $stmt->execute();
            $row = $stmt->fetch();
            
            return $row['count'] > 0;
        } catch (PDOException $e) {
            error_log("ParticipantModel::emailExists Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la vérification de l'email.");
        }
    }

    /**
     * Get a participant ID by email
     * 
     * @param string $email Email of the participant
     * @return int|bool The ID of the participant or false if not found
     */
    public function getParticipantIdByEmail($email) {
        try {
            $query = "SELECT id FROM {$this->table} WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            
            // Bind parameter
            $stmt->bindParam(':email', $email);
            
            // Execute the query
            $stmt->execute();
            $row = $stmt->fetch();
            
            return $row ? $row['id'] : false;
        } catch (PDOException $e) {
            error_log("ParticipantModel::getParticipantIdByEmail Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la récupération du participant.");
        }
    }

    /**
     * Read all participants
     * 
     * @return array An array of participants
     */
    public function readAll() {
        try {
            $query = "SELECT * FROM {$this->table} ORDER BY nom ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("ParticipantModel::readAll Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la récupération des participants.");
        }
    }

    /**
     * Read a single participant by ID
     * 
     * @param int $id The ID of the participant
     * @return array|false Participant data or false if not found
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
            error_log("ParticipantModel::readOne Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la récupération du participant.");
        }
    }

    /**
     * Update a participant
     * 
     * @param int $id The ID of the participant
     * @param string $nom Name of the participant
     * @param string $email Email of the participant
     * @return bool True if update was successful, false otherwise
     */
    public function update($id, $nom, $email) {
        try {
            $query = "UPDATE {$this->table} SET nom = :nom, email = :email WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id', $id);
            
            // Execute the query
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("ParticipantModel::update Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la mise à jour du participant.");
        }
    }

    /**
     * Delete a participant
     * 
     * @param int $id The ID of the participant to delete
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
            error_log("ParticipantModel::delete Error: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la suppression du participant.");
        }
    }
}
?>
