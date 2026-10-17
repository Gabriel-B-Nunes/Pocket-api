CREATE TABLE `group` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid BINARY(16) NOT NULL UNIQUE,
    userUuid BINARY(16) NOT NULL,
    name VARCHAR(100) NOT NULL,
    status VARCHAR(10) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP,
    CONSTRAINT fk_group_user_uuid
        FOREIGN KEY (userUuid) REFERENCES `user`(uuid)
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT groupNameUserUuid
        UNIQUE (name, userUuid)
);