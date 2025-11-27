-- Corrected SQL for creating OrderMeta table
-- For SQLite

PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS OrderMeta (
    Id INTEGER PRIMARY KEY AUTOINCREMENT,
    OrderId INTEGER NOT NULL,
    MetaKey TEXT NOT NULL,
    MetaValue TEXT,
    FOREIGN KEY (OrderId) REFERENCES Orders(Id) ON DELETE CASCADE
);

