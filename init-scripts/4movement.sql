CREATE TABLE movement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid BINARY(16) NOT NULL UNIQUE,
    userUuid BINARY(16) NOT NULL,
    description VARCHAR(255) NOT NULL,
    transactionDate VARCHAR(30) NOT NULL,
    financialType VARCHAR(10) NOT NULL,
    recurrence VARCHAR(10) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP,
    CONSTRAINT fk_movement_user_uuid
        FOREIGN KEY (userUuid) REFERENCES `user`(uuid)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);
