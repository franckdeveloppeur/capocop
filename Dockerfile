# Utiliser PHP 8.2 avec FPM
FROM php:8.2-fpm

# Arguments de build
ARG user=www
ARG uid=1000

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP requises
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer Node.js et npm (version LTS)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Créer un utilisateur système pour exécuter les commandes Composer et Artisan
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Définir le répertoire de travail
WORKDIR /var/www

# Copier les fichiers de configuration personnalisés
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Copier les fichiers du projet
COPY --chown=$user:$user . /var/www

# Installer les dépendances PHP avec Composer
USER $user
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Installer les dépendances Node.js et compiler les assets
RUN npm install && npm run build

# Retourner à l'utilisateur root pour les permissions finales
USER root

# Définir les permissions
RUN chown -R $user:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Exposer le port 80
EXPOSE 80

# Démarrer Supervisor (qui gérera Nginx et PHP-FPM)
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

