# 🔧 Dépannage Docker - Guide Rapide

## ❌ Erreur : "composer install failed"

### Solution 1 : Utiliser le script simplifié

```powershell
# Windows
.\docker-build-simple.ps1
```

Ce script :
- ✅ Nettoie les anciens conteneurs
- ✅ Construit sans cache
- ✅ Configure automatiquement tout

### Solution 2 : Build manuel sans cache

```bash
# Nettoyer
docker-compose down -v
docker system prune -a

# Reconstruire
docker-compose build --no-cache
docker-compose up -d
```

### Solution 3 : Vérifier la mémoire Docker

**Windows/Mac :**
1. Docker Desktop → Settings → Resources
2. Augmenter la mémoire à **4 GB minimum**
3. Redémarrer Docker Desktop

## ❌ Erreur : "Extensions PHP manquantes"

C'est normal ! Les warnings sur les extensions sont informatifs. Le Dockerfile installe tout ce qui est nécessaire.

## ❌ Erreur : "Cannot connect to MySQL"

```bash
# Attendre que MySQL soit prêt (peut prendre 30-60 secondes)
docker-compose logs db

# Vérifier le status
docker-compose ps

# Redémarrer MySQL
docker-compose restart db
```

## ❌ Erreur : "Port 8000 already in use"

```bash
# Trouver le processus qui utilise le port
netstat -ano | findstr :8000

# Tuer le processus (remplacez PID)
taskkill /F /PID <PID>

# Ou changer le port dans docker-compose.yml
ports:
  - "8001:80"  # Utiliser 8001 au lieu de 8000
```

## ❌ Erreur : "ENOSPC: no space left on device"

```bash
# Nettoyer Docker
docker system prune -a --volumes

# Libérer de l'espace
docker volume prune
docker image prune -a
```

## ❌ Build très lent ou timeout

```powershell
# Augmenter le timeout de build
$env:COMPOSE_HTTP_TIMEOUT=300
docker-compose build
```

## ❌ Erreur : "npm install failed"

Le Dockerfile a été corrigé pour utiliser `npm ci` qui est plus fiable.

Si le problème persiste :

```bash
# Construire sans les assets Node.js
# Modifiez temporairement le Dockerfile et commentez ces lignes:
# RUN npm ci --prefer-offline --no-audit
# RUN npm run build
```

## ✅ Commandes de diagnostic

```bash
# Voir tous les conteneurs
docker-compose ps

# Voir les logs en temps réel
docker-compose logs -f

# Logs d'un service spécifique
docker-compose logs -f app
docker-compose logs -f db

# Entrer dans le conteneur
docker-compose exec app bash

# Vérifier PHP
docker-compose exec app php -v
docker-compose exec app php -m  # Extensions installées

# Vérifier Composer
docker-compose exec app composer --version

# Tester la connexion MySQL
docker-compose exec app php artisan db:show
```

## 🔄 Reset complet

Si rien ne fonctionne, reset complet :

```powershell
# 1. Tout arrêter
docker-compose down -v

# 2. Nettoyer Docker
docker system prune -a --volumes

# 3. Supprimer les fichiers générés
Remove-Item -Recurse -Force vendor, node_modules, bootstrap/cache/* -ErrorAction SilentlyContinue

# 4. Reconstruire
.\docker-build-simple.ps1
```

## 💡 Conseils de performance

### Windows avec WSL2

1. Mettez le projet dans le filesystem WSL2 (plus rapide)
2. Augmentez la mémoire WSL2 :

Créez `C:\Users\<votre-nom>\.wslconfig` :

```ini
[wsl2]
memory=4GB
processors=4
```

### Désactiver l'antivirus temporairement

L'antivirus peut ralentir Docker. Ajoutez des exclusions pour :
- Le dossier du projet
- `C:\ProgramData\Docker`
- `%LOCALAPPDATA%\Docker`

## 🆘 Toujours bloqué ?

1. Vérifiez les logs complets :
   ```bash
   docker-compose logs > logs.txt
   ```

2. Vérifiez la version de Docker :
   ```bash
   docker --version
   docker-compose --version
   ```

3. Versions minimales requises :
   - Docker : 20.10+
   - Docker Compose : 2.0+

4. Essayez avec une image PHP différente :
   Dans le Dockerfile, changez :
   ```dockerfile
   FROM php:8.2-fpm
   # en
   FROM php:8.2-fpm-alpine
   ```

## 📚 Ressources

- [Documentation Docker](https://docs.docker.com)
- [Laravel Docker](https://laravel.com/docs/deployment#docker)
- [Composer en Docker](https://hub.docker.com/_/composer)



