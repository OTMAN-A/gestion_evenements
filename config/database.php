<?php
/**
 * Database Connection Class
 * Handles the connection to MySQL database via PDO
 */
class Database {
    private $host = "localhost";
    private $dbname = "gestion_evenements";
    private $username = "root";
    private $password = "";
    private $conn;

    /**
     * Constructor that establishes the database connection
     */
    public function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            // Log the error details but show a generic message to users
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("Une erreur de connexion à la base de données est survenue.");
        }
    }

    /**
     * Get the database connection
     * @return PDO Returns the PDO connection object
     */
    public function getConnection() {
        return $this->conn;
    }
}
?>
