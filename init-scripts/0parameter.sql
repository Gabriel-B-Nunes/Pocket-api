CREATE TABLE parameter (
    name VARCHAR(100) NOT NULL PRIMARY KEY,
    parameterType INT NOT NULL,
    value VARCHAR(255),
    description VARCHAR(255) NOT NULL,
    required BOOL NOT NULL
);