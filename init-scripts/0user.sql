CREATE TABLE `user` (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    userName VARCHAR(100) NOT NULL,
    userEmail VARCHAR(100) UNIQUE,
    userCellphoneNumber VARCHAR(15) UNIQUE,
    userPassword VARCHAR(255),
    userStatus INT NOT NULL,
    userCreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);