CREATE TABLE `movement` (
    movementId INT AUTO_INCREMENT PRIMARY KEY,
    movementDescription VARCHAR(100) NOT NULL,
    movementFinancialType INT NOT NULL,
    movementRecurring INT NOT NULL,
    movementIssueDate DATE NOT NULL,
    movementCreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);