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
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP requises
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

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

# Copier composer.json et composer.lock en premier (pour le cache Docker)
COPY --chown=$user:$user composer.json composer.lock ./

# Passer à l'utilisateur www pour composer
USER $user

# Installer les dépendances PHP (sans scripts pour éviter les erreurs)
RUN composer install --no-scripts --no-autoloader --prefer-dist --no-interaction

# Copier le reste des fichiers du projet
USER root
COPY --chown=$user:$user . /var/www

# Générer l'autoloader et exécuter les scripts post-install
USER $user
RUN composer dump-autoload --optimize

# Créer les répertoires nécessaires s'ils n'existent pas
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    bootstrap/cache

# Installer les dépendances Node.js et compiler les assets
RUN npm ci --prefer-offline --no-audit \
    && npm run build \
    && rm -rf node_modules

# Retourner à l'utilisateur root pour les permissions finales
USER root

# Définir les permissions
RUN chown -R $user:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Exposer le port 80
EXPOSE 80

# Démarrer Supervisor (qui gérera Nginx et PHP-FPM)
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

