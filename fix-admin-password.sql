-- Quick fix for admin password
-- Run this in phpMyAdmin if you've already imported the database

UPDATE `adminusers` 
SET `PasswordHash` = '$2y$10$U4KWXvhOrjvpe7JO.kG2x.D.U6.zN6kz7eO7Arg3wuNSbfqf1bcFa' 
WHERE `Username` = 'admin';

