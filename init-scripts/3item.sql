CREATE TABLE `item` (
    itemId INT AUTO_INCREMENT PRIMARY KEY,
    itemName VARCHAR(100) NOT NULL,
    itemMeasurementUnit INT NOT NULL,
    groupId INT NOT NULL,
    itemFinancialType INT NOT NULL,
    itemStatus INT NOT NULL,
    itemCreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_item_group_id
        FOREIGN KEY (groupId) REFERENCES `group`(groupId)
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
);