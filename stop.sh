#!/usr/bin/env bash
set -e

ROOT=$(dirname "$0")
cd "$ROOT"
docker compose down
echo "HackCRM stopped."
