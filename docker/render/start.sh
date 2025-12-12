#!/bin/bash
# Script de démarrage pour Render

set -e

echo "🚀 Démarrage de l'application Capocop sur Render..."

# Attendre que la base de données soit prête
echo "⏳ Attente de la base de données..."
max_attempts=30
attempt=0

while [ $attempt -lt $max_attempts ]; do
    if php artisan db:show > /dev/null 2>&1; then
        echo "✅ Base de données accessible"
        break
    fi
    attempt=$((attempt + 1))
    echo "Tentative $attempt/$max_attempts..."
    sleep 2
done

if [ $attempt -eq $max_attempts ]; then
    echo "⚠️ Impossible de se connecter à la base de données, démarrage quand même..."
fi

# Créer le lien symbolique pour le storage si nécessaire
echo "📂 Configuration du storage..."
php artisan storage:link || true

# Exécuter les migrations (avec --force pour production)
echo "🗄️ Exécution des migrations..."
php artisan migrate --force --no-interaction

# Optimiser l'application pour la production
echo "⚡ Optimisation de l'application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "✅ Application prête!"

# Démarrer Supervisor qui gérera Nginx et PHP-FPM
echo "🌐 Démarrage des services web..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf

