CREATE TABLE movementItem (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid BINARY(16) NOT NULL UNIQUE,
    userUuid BINARY(16) NOT NULL,
    itemUuid BINARY(16) NOT NULL,
    unitValue INT NOT NULL,
    quantity INT NOT NULL,
    unity VARCHAR(10) NOT NULL,
    currency VARCHAR(10) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP,
    CONSTRAINT fk_movementitem_user_uuid
        FOREIGN KEY (userUuid) REFERENCES `user`(uuid)
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT fk_movementitem_item_uuid
        FOREIGN KEY (itemUuid) REFERENCES `item`(uuid)
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
);