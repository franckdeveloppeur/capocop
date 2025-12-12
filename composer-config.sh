#!/bin/bash
# Configuration Composer pour éviter les timeouts

echo "⚙️  Configuration de Composer pour optimiser l'installation..."

docker-compose -f docker-compose-dev.yml exec -T app composer config --global process-timeout 2000
docker-compose -f docker-compose-dev.yml exec -T app composer config --global cache-files-maxsize "512MiB"
docker-compose -f docker-compose-dev.yml exec -T app composer config --global discard-changes true

echo "✅ Composer configuré"

