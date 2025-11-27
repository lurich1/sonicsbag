-- Corrected SQL for creating OrderMeta table
-- For MySQL

CREATE TABLE IF NOT EXISTS OrderMeta (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    OrderId INT NOT NULL,
    MetaKey VARCHAR(255) NOT NULL,
    MetaValue TEXT,
    FOREIGN KEY (OrderId) REFERENCES Orders(Id) ON DELETE CASCADE
);

