#!/usr/bin/env bash
set -e

MYSQL="C:/Program Files/MySQL/MySQL Server 8.3/bin/mysql.exe"
ROOT=$(dirname "$0")

echo "======================================"
echo "  HackCRM — First-time setup"
echo "======================================"

# ── 1. Tool checks ──────────────────────────────────────────────────────────
echo ""
echo "[1/7] Checking required tools..."

if ! command -v docker &>/dev/null; then
  echo "✗ Docker not found. Install Docker Desktop: https://www.docker.com/products/docker-desktop"
  exit 1
fi
echo "  ✓ Docker: $(docker --version)"

if ! docker info &>/dev/null; then
  echo "  Docker Desktop is not running. Starting it..."
  start "" "C:/Program Files/Docker/Docker/Docker Desktop.exe"
  echo "  Waiting for Docker engine (up to 90s)..."
  for i in $(seq 1 18); do
    sleep 5
    docker info &>/dev/null && break
    echo "    ...still waiting ($((i*5))s)"
  done
  docker info &>/dev/null || { echo "✗ Docker did not start in time. Start Docker Desktop manually and re-run."; exit 1; }
fi
echo "  ✓ Docker engine is running"

# ── 2. MySQL check ──────────────────────────────────────────────────────────
echo ""
echo "[2/7] Checking MySQL..."
if ! "$MYSQL" -u hackathon -phackathon_secret -e "USE hackathon_crm; SELECT 'DB OK';" &>/dev/null; then
  echo "✗ Cannot connect to MySQL as hackathon@localhost."
  echo "  Make sure MySQL83 service is running and the database/user were created."
  echo "  Run the setup SQL from the README, then re-run this script."
  exit 1
fi
echo "  ✓ MySQL reachable, database hackathon_crm accessible"

# ── 3. .env files ───────────────────────────────────────────────────────────
echo ""
echo "[3/7] Setting up .env files..."

if [ ! -f "$ROOT/backend/.env" ]; then
  cp "$ROOT/backend/.env.example" "$ROOT/backend/.env"
  sed -i 's/APP_NAME=.*/APP_NAME=HackCRM/' "$ROOT/backend/.env"
  sed -i 's/DB_HOST=.*/DB_HOST=host.docker.internal/' "$ROOT/backend/.env"
  echo "  ✓ backend/.env created"
else
  echo "  ✓ backend/.env already exists"
fi

if [ ! -f "$ROOT/frontend/.env" ]; then
  echo "VITE_API_URL=http://localhost:8000/api" > "$ROOT/frontend/.env"
  echo "  ✓ frontend/.env created"
else
  echo "  ✓ frontend/.env already exists"
fi

# ── 4. Build & start Docker ─────────────────────────────────────────────────
echo ""
echo "[4/7] Building Docker images (this may take several minutes)..."
cd "$ROOT"
docker compose down --remove-orphans
docker compose up -d --build
echo "  ✓ Containers started"

# ── 5. Wait for app container to be ready ───────────────────────────────────
echo ""
echo "[5/7] Waiting for app container to finish migrations..."
for i in $(seq 1 30); do
  sleep 2
  if docker logs crm_app 2>&1 | grep -q "ready to handle connections"; then
    break
  fi
  echo "    ...waiting ($((i*2))s)"
done

# ── 6. Run seeder ────────────────────────────────────────────────────────────
echo ""
echo "[6/7] Seeding database..."
docker compose exec app php artisan db:seed --force
echo "  ✓ Database seeded"

# ── 7. Verify ────────────────────────────────────────────────────────────────
echo ""
echo "[7/7] Verifying..."

API=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/api/events/active)
if [ "$API" = "200" ]; then
  echo "  ✓ API responding (http://localhost:8000/api)"
else
  echo "  ✗ API not responding (HTTP $API) — check: docker compose logs nginx"
  exit 1
fi

FE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:3000)
if [ "$FE" = "200" ]; then
  echo "  ✓ Frontend responding (http://localhost:3000)"
else
  echo "  ✗ Frontend not responding (HTTP $FE) — check: docker compose logs frontend"
  exit 1
fi

LOGIN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@hackathon.com","password":"Admin1234!"}')
if echo "$LOGIN" | grep -q '"token"'; then
  echo "  ✓ Admin login successful"
else
  echo "  ✗ Admin login failed — response: $LOGIN"
  exit 1
fi

echo ""
echo "======================================"
echo "  HackCRM is ready!"
echo "======================================"
echo "  Frontend : http://localhost:3000"
echo "  API      : http://localhost:8000/api"
echo "  Admin    : admin@hackathon.com / Admin1234!"
echo "======================================"
