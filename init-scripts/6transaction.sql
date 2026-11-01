CREATE TABLE `transaction` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid BINARY(16) NOT NULL UNIQUE,
    userUuid BINARY(16) NOT NULL,
    installmentUuid BINARY(16) NOT NULL,
    paymentDate VARCHAR(30) NOT NULL,
    paymentValue INT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP,
    CONSTRAINT fk_transaction_user_uuid
        FOREIGN KEY (userUuid) REFERENCES `user`(uuid)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_transaction_installment_uuid
        FOREIGN KEY (installmentUuid) REFERENCES installment(uuid)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);
