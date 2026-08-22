CREATE TABLE `superGroup` (
    superGroupId INT AUTO_INCREMENT PRIMARY KEY,
    superGroupName VARCHAR(100) NOT NULL,
    superGroupStatus INT NOT NULL,
    superGroupCreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);