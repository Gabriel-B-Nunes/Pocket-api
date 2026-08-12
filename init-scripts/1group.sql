CREATE TABLE `group` (
    groupId INT AUTO_INCREMENT PRIMARY KEY,
    groupName VARCHAR(100) NOT NULL,
    groupStatus INT NOT NULL,
    superGroupId INT NOT NULL,
    groupCreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_group_supergroup_id
        FOREIGN KEY (superGroupId) REFERENCES `superGroup`(superGroupId)
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
);