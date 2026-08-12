CREATE TABLE responsible (
    responsibleId INT AUTO_INCREMENT PRIMARY KEY,
    responsibleName VARCHAR(100) NOT NULL,
    responsibleEmailAddress VARCHAR(100) UNIQUE,
    responsibleCellphoneNumber VARCHAR(15) UNIQUE,
    responsibleStatus INT NOT NULL,
    responsibleCreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);