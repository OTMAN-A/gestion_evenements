-- Database: gestion_evenements
CREATE DATABASE IF NOT EXISTS gestion_evenements;
USE gestion_evenements;

-- Table: events
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    date_evenement DATE NOT NULL,
    description TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: participants
CREATE TABLE IF NOT EXISTS participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: inscriptions
CREATE TABLE IF NOT EXISTS inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    participant_id INT NOT NULL,
    date_inscription DATETIME NOT NULL,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
    UNIQUE KEY event_participant (event_id, participant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data for testing (optional)
INSERT INTO events (titre, date_evenement, description) VALUES
('Conférence PHP 2023', '2023-12-15', 'Une conférence sur les dernières tendances et pratiques en PHP.'),
('Atelier MySQL Avancé', '2023-12-20', 'Un atelier pratique sur l\'optimisation des bases de données MySQL.'),
('Webinaire Sécurité Web', '2024-01-10', 'Un webinaire sur les meilleures pratiques de sécurité pour les applications web.');

INSERT INTO participants (nom, email) VALUES
('Jean Dupont', 'jean.dupont@example.com'),
('Marie Martin', 'marie.martin@example.com'),
('Pierre Durand', 'pierre.durand@example.com');

INSERT INTO inscriptions (event_id, participant_id, date_inscription) VALUES
(1, 1, NOW()),
(1, 2, NOW()),
(2, 3, NOW()),
(3, 1, NOW());
