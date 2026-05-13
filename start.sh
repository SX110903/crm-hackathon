#!/usr/bin/env bash
set -e

ROOT=$(dirname "$0")

echo "Starting HackCRM..."

# Start Docker Desktop if engine is not running
if ! docker info &>/dev/null 2>&1; then
  echo "  Docker engine not running. Starting Docker Desktop..."
  start "" "C:/Program Files/Docker/Docker/Docker Desktop.exe"
  for i in $(seq 1 18); do
    sleep 5
    docker info &>/dev/null 2>&1 && break
    echo "    ...waiting for Docker ($((i*5))s)"
  done
  docker info &>/dev/null 2>&1 || { echo "✗ Docker did not start. Open Docker Desktop manually and retry."; exit 1; }
fi

# Start containers
cd "$ROOT"
docker compose up -d

# Wait until app is healthy
echo "  Waiting for containers to be ready..."
for i in $(seq 1 20); do
  sleep 3
  STATUS=$(docker inspect --format='{{.State.Status}}' crm_app 2>/dev/null || echo "missing")
  if [ "$STATUS" = "running" ]; then
    break
  fi
  echo "    ...crm_app status: $STATUS ($((i*3))s)"
done

echo ""
echo "======================================"
echo "  HackCRM is running!"
echo "  Frontend : http://localhost:3000"
echo "  API      : http://localhost:8000/api"
echo "  Admin    : admin@hackathon.com / Admin1234!"
echo "======================================"
