-- HackCRM — Database & user setup
-- Run this file in MySQL Workbench as root before running setup.sh

CREATE DATABASE IF NOT EXISTS hackathon_crm
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

DROP USER IF EXISTS 'hackathon'@'localhost';
DROP USER IF EXISTS 'hackathon'@'%';
DROP USER IF EXISTS 'hackathon'@'172.17.0.1';

CREATE USER 'hackathon'@'localhost'   IDENTIFIED BY 'hackathon_secret';
CREATE USER 'hackathon'@'%'           IDENTIFIED BY 'hackathon_secret';
CREATE USER 'hackathon'@'172.17.0.1'  IDENTIFIED BY 'hackathon_secret';

GRANT ALL PRIVILEGES ON hackathon_crm.* TO 'hackathon'@'localhost';
GRANT ALL PRIVILEGES ON hackathon_crm.* TO 'hackathon'@'%';
GRANT ALL PRIVILEGES ON hackathon_crm.* TO 'hackathon'@'172.17.0.1';

FLUSH PRIVILEGES;

-- Verify (optional — run separately to confirm):
-- SELECT user, host FROM mysql.user WHERE user = 'hackathon';
