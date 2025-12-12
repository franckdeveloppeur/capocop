# 🐳 Configuration Docker pour Capocop

Ce guide vous explique comment configurer et démarrer votre projet Laravel Capocop avec Docker.

## 📋 Prérequis

- Docker Desktop (Windows/Mac) ou Docker Engine + Docker Compose (Linux)
- Au moins 4 GB de RAM disponible pour Docker

## 🏗️ Architecture

Le projet utilise les services suivants :

- **app** : Application Laravel avec PHP 8.2-FPM + Nginx
- **db** : MySQL 8.4 (sécurisé et performant)
- **redis** : Redis 7 pour le cache et les sessions
- **phpmyadmin** : Interface web pour gérer MySQL
- **queue** : Worker Laravel pour les tâches en arrière-plan

## 🚀 Installation et Démarrage

### 1. Configuration de l'environnement

Copiez le template de configuration :

```bash
cp docker/env-template.txt .env
```

Ensuite, éditez le fichier `.env` et modifiez au minimum ces valeurs :

```env
APP_KEY=                    # Sera généré automatiquement
DB_PASSWORD=VotreMotDePasse123!
DB_ROOT_PASSWORD=MotDePasseRoot456!
REDIS_PASSWORD=RedisPass789!
```

### 2. Construction des images Docker

```bash
docker-compose build
```

### 3. Démarrage des conteneurs

```bash
docker-compose up -d
```

### 4. Installation initiale de Laravel

Exécutez ces commandes pour initialiser l'application :

```bash
# Générer la clé d'application
docker-compose exec app php artisan key:generate

# Exécuter les migrations
docker-compose exec app php artisan migrate

# Créer le lien symbolique pour le storage
docker-compose exec app php artisan storage:link

# Optimiser l'application
docker-compose exec app php artisan optimize
```

### 5. Accès à l'application

- **Application** : http://localhost:8000
- **phpMyAdmin** : http://localhost:8080
  - Serveur : `db`
  - Utilisateur : `root`
  - Mot de passe : celui défini dans `DB_ROOT_PASSWORD`

## 📦 Commandes utiles

### Gestion des conteneurs

```bash
# Démarrer les services
docker-compose up -d

# Arrêter les services
docker-compose down

# Voir les logs
docker-compose logs -f

# Voir les logs d'un service spécifique
docker-compose logs -f app

# Redémarrer un service
docker-compose restart app
```

### Commandes Laravel

```bash
# Artisan
docker-compose exec app php artisan [commande]

# Composer
docker-compose exec app composer [commande]

# NPM
docker-compose exec app npm [commande]

# Accès au shell du conteneur
docker-compose exec app bash

# Accès à MySQL
docker-compose exec db mysql -u root -p
```

### Migrations et Seeders

```bash
# Exécuter les migrations
docker-compose exec app php artisan migrate

# Réinitialiser et migrer
docker-compose exec app php artisan migrate:fresh

# Exécuter les seeders
docker-compose exec app php artisan db:seed

# Migration + Seed
docker-compose exec app php artisan migrate:fresh --seed
```

### Cache et Optimisation

```bash
# Vider tous les caches
docker-compose exec app php artisan optimize:clear

# Optimiser l'application
docker-compose exec app php artisan optimize

# Vider le cache de configuration
docker-compose exec app php artisan config:clear

# Vider le cache des routes
docker-compose exec app php artisan route:clear

# Vider le cache des vues
docker-compose exec app php artisan view:clear
```

## 🔒 Sécurité

### Mots de passe

**IMPORTANT** : Changez tous les mots de passe par défaut dans le fichier `.env` :

- `DB_PASSWORD` : Mot de passe de l'utilisateur MySQL
- `DB_ROOT_PASSWORD` : Mot de passe root MySQL
- `REDIS_PASSWORD` : Mot de passe Redis

### Recommandations

1. Utilisez des mots de passe forts (min. 16 caractères)
2. Ne commitez JAMAIS le fichier `.env` dans Git
3. En production, utilisez `APP_DEBUG=false`
4. Configurez un certificat SSL/TLS pour HTTPS
5. Limitez l'accès à phpMyAdmin (désactivez-le en production)

## 🗄️ Base de données

### Connexion MySQL

Les paramètres de connexion sont :

- **Host** : `db` (depuis l'application) ou `localhost:3306` (depuis votre machine)
- **Base de données** : `capocop`
- **Utilisateur** : `capocop_user`
- **Mot de passe** : Celui défini dans `.env`

### Backup de la base de données

```bash
# Créer un backup
docker-compose exec db mysqldump -u root -p capocop > backup_$(date +%Y%m%d_%H%M%S).sql

# Restaurer un backup
docker-compose exec -T db mysql -u root -p capocop < backup.sql
```

## 🔧 Dépannage

### Le conteneur ne démarre pas

```bash
# Vérifier les logs
docker-compose logs app

# Reconstruire les images
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Erreur de permissions

```bash
# Sur Linux/Mac
sudo chown -R $USER:$USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Depuis le conteneur
docker-compose exec app chown -R www:www-data /var/www
docker-compose exec app chmod -R 755 /var/www/storage
```

### Base de données non accessible

```bash
# Vérifier que MySQL est prêt
docker-compose exec db mysqladmin ping -h localhost -u root -p

# Recréer la base de données
docker-compose down -v
docker-compose up -d
```

### Erreur "No application encryption key has been specified"

```bash
docker-compose exec app php artisan key:generate
```

## 📊 Performance

### Configuration de production

Pour optimiser les performances en production :

1. Modifiez `.env` :
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. Optimisez l'application :
   ```bash
   docker-compose exec app php artisan config:cache
   docker-compose exec app php artisan route:cache
   docker-compose exec app php artisan view:cache
   docker-compose exec app composer install --optimize-autoloader --no-dev
   ```

### Monitoring

```bash
# Utilisation des ressources
docker stats

# Espace disque
docker system df
```

## 🧹 Nettoyage

```bash
# Arrêter et supprimer les conteneurs
docker-compose down

# Supprimer aussi les volumes (ATTENTION : efface les données)
docker-compose down -v

# Nettoyer le système Docker
docker system prune -a
```

## 📝 Notes

- Les données MySQL sont persistées dans un volume Docker nommé `db-data`
- Les données Redis sont persistées dans un volume Docker nommé `redis-data`
- Les fichiers de l'application sont montés en volume pour le développement
- Pour la production, il est recommandé de ne pas monter les fichiers sources

## 🆘 Support

Pour plus d'informations :
- Documentation Laravel : https://laravel.com/docs
- Documentation Docker : https://docs.docker.com
- Documentation MySQL : https://dev.mysql.com/doc/

## 📜 Licence

Ce projet utilise la licence MIT.

