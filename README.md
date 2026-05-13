# HackCRM

## Quick start

```bash
# First time on a new machine:
bash setup.sh

# Every time after:
bash start.sh

# To stop:
bash stop.sh
```

**URLs once running:**
- Frontend: http://localhost:3000
- API:      http://localhost:8000/api
- Admin:    admin@hackathon.com / Admin1234!

---

## Prerequisites (first-time only)

- [Docker Desktop](https://www.docker.com/products/docker-desktop) (required)
- MySQL 8.x running locally with:
  - Database: `hackathon_crm`
  - User: `hackathon` / password: `hackathon_secret` with full privileges on that DB

To create the database and user (run in MySQL as root):

```sql
CREATE DATABASE IF NOT EXISTS hackathon_crm
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

DROP USER IF EXISTS 'hackathon'@'localhost';
DROP USER IF EXISTS 'hackathon'@'%';
DROP USER IF EXISTS 'hackathon'@'172.17.0.1';

CREATE USER 'hackathon'@'localhost' IDENTIFIED BY 'hackathon_secret';
CREATE USER 'hackathon'@'%'         IDENTIFIED BY 'hackathon_secret';
CREATE USER 'hackathon'@'172.17.0.1' IDENTIFIED BY 'hackathon_secret';

GRANT ALL PRIVILEGES ON hackathon_crm.* TO 'hackathon'@'localhost';
GRANT ALL PRIVILEGES ON hackathon_crm.* TO 'hackathon'@'%';
GRANT ALL PRIVILEGES ON hackathon_crm.* TO 'hackathon'@'172.17.0.1';

FLUSH PRIVILEGES;
```

## Stack

| Layer    | Tech                          | Port |
|----------|-------------------------------|------|
| Frontend | React 19 + TypeScript + Vite  | 3000 |
| API      | Laravel 11 (CQRS, Sanctum)    | 8000 |
| PHP-FPM  | PHP 8.4                       | 9000 |
| Database | MySQL 8.3 (local, not Docker) | 3306 |
