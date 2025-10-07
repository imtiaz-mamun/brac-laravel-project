-- MySQL initialization script for Microfinance Laravel API

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS microfinance_db;
USE microfinance_db;

-- Create user if not exists
CREATE USER IF NOT EXISTS 'microfinance_user'@'%' IDENTIFIED BY 'microfinance_password';
GRANT ALL PRIVILEGES ON microfinance_db.* TO 'microfinance_user'@'%';

-- Optimize MySQL settings for Laravel
SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- Create indexes for better performance (these will be created by Laravel migrations)
-- This file ensures database is properly set up before Laravel runs

FLUSH PRIVILEGES;