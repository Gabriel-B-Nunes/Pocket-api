CREATE TABLE `movementItem` (
    movementItemId INT AUTO_INCREMENT PRIMARY KEY,
    movementItemItemId INT NOT NULL,
    movementItemMovementId INT NOT NULL,
    movementItemQuantity INT NOT NULL,
    movementItemItemMeasurementUnity INT NOT NULL,
    movementItemUnityPrice INT NOT NULL,
    movementItemTotalPrice INT NOT NULL,
    movementItemCreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_item_id
        FOREIGN KEY (movementItemItemId) REFERENCES `item`(itemId)
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT fk_movement_id
        FOREIGN KEY (movementItemMovementId) REFERENCES `movement`(movementId)
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
);