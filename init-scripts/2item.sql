CREATE TABLE item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid BINARY(16) NOT NULL UNIQUE,
    userUuid BINARY(16) NOT NULL,
    groupUuid BINARY(16) NOT NULL,
    name VARCHAR(100) NOT NULL,
    status VARCHAR(10) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP,
    CONSTRAINT fk_item_user_uuid
        FOREIGN KEY (userUuid) REFERENCES `user`(uuid)
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT fk_item_group_uuid
        FOREIGN KEY (groupUuid) REFERENCES `group`(uuid)
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT itemNameUserUuid
        UNIQUE (name, userUuid)
);