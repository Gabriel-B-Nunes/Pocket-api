CREATE TABLE installment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid BINARY(16) NOT NULL UNIQUE,
    userUuid BINARY(16) NOT NULL,
    movementUuid BINARY(16) NOT NULL,
    dueDate VARCHAR(30) NOT NULL,
    dueValue INT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP,
    CONSTRAINT fk_installment_user_uuid
        FOREIGN KEY (userUuid) REFERENCES `user`(uuid)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_installment_movement_uuid
        FOREIGN KEY (movementUuid) REFERENCES movement(uuid)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);
