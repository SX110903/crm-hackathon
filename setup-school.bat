@echo off
echo ================================
echo  HackCRM School Setup
echo ================================
echo.
echo [1/5] Starting MySQL...
net start MySQL83
timeout /t 3 /nobreak >nul
echo.
echo [2/5] Generating database-setup.sql...
echo CREATE DATABASE IF NOT EXISTS hackathon_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; > database-setup.sql
echo DROP USER IF EXISTS 'hackathon'@'localhost'; >> database-setup.sql
echo DROP USER IF EXISTS 'hackathon'@'%%'; >> database-setup.sql
echo CREATE USER 'hackathon'@'localhost' IDENTIFIED BY 'hackathon_secret'; >> database-setup.sql
echo CREATE USER 'hackathon'@'%%' IDENTIFIED BY 'hackathon_secret'; >> database-setup.sql
echo GRANT ALL PRIVILEGES ON hackathon_crm.* TO 'hackathon'@'localhost'; >> database-setup.sql
echo GRANT ALL PRIVILEGES ON hackathon_crm.* TO 'hackathon'@'%%'; >> database-setup.sql
echo FLUSH PRIVILEGES; >> database-setup.sql
echo.
echo >>> MANUAL STEP REQUIRED <<<
echo Open MySQL Workbench and run: database-setup.sql
echo Then press any key to continue...
pause >nul
echo.
echo [3/5] Starting Docker containers...
docker compose up -d
timeout /t 20 /nobreak >nul
echo.
echo [4/5] Running migrations...
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
echo.
echo [5/5] Creating admin user...
docker compose exec app php artisan tinker --execute="App\Models\User::updateOrCreate(['email'=>'admin@hackathon.com'],['name'=>'Administrator','password'=>bcrypt('Admin1234!'),'role'=>'admin','is_active'=>true]); echo 'OK';"
echo.
echo ================================
echo  HackCRM is ready!
echo  Frontend : http://localhost:3000
echo  API      : http://localhost:8000/api
echo  Admin    : admin@hackathon.com
echo  Password : Admin1234!
echo ================================
pause
