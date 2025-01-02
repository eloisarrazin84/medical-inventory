CREATE TABLE IF NOT EXISTS sacs_medicaux (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS medicaments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    quantite INT DEFAULT 0,
    date_expiration DATE,
    sac_id INT,
    FOREIGN KEY (sac_id) REFERENCES sacs_medicaux(id)
);
