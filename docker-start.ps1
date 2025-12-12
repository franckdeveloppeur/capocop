# Script PowerShell pour démarrer le projet Docker Capocop
# Usage: .\docker-start.ps1

Write-Host "🐳 Démarrage du projet Capocop avec Docker..." -ForegroundColor Cyan

# Vérifier si Docker est en cours d'exécution
Write-Host "`n📦 Vérification de Docker..." -ForegroundColor Yellow
$dockerRunning = docker info 2>&1 | Select-String "Server Version"
if (-not $dockerRunning) {
    Write-Host "❌ Docker n'est pas en cours d'exécution. Veuillez démarrer Docker Desktop." -ForegroundColor Red
    exit 1
}
Write-Host "✅ Docker est opérationnel" -ForegroundColor Green

# Vérifier si le fichier .env existe
Write-Host "`n🔍 Vérification du fichier .env..." -ForegroundColor Yellow
if (-not (Test-Path ".env")) {
    Write-Host "⚠️  Le fichier .env n'existe pas." -ForegroundColor Yellow
    Write-Host "📋 Création du fichier .env depuis le template..." -ForegroundColor Cyan
    Copy-Item "docker/env-template.txt" ".env"
    
    # Générer des mots de passe aléatoires
    $dbPassword = -join ((65..90) + (97..122) + (48..57) | Get-Random -Count 16 | ForEach-Object {[char]$_})
    $dbRootPassword = -join ((65..90) + (97..122) + (48..57) | Get-Random -Count 16 | ForEach-Object {[char]$_})
    $redisPassword = -join ((65..90) + (97..122) + (48..57) | Get-Random -Count 16 | ForEach-Object {[char]$_})
    
    # Remplacer les mots de passe dans le fichier .env
    (Get-Content ".env") -replace "DB_PASSWORD=secret_password", "DB_PASSWORD=$dbPassword" `
        -replace "DB_ROOT_PASSWORD=root_password", "DB_ROOT_PASSWORD=$dbRootPassword" `
        -replace "REDIS_PASSWORD=redis_password", "REDIS_PASSWORD=$redisPassword" | 
        Set-Content ".env"
    
    Write-Host "✅ Fichier .env créé avec des mots de passe sécurisés!" -ForegroundColor Green
} else {
    Write-Host "✅ Fichier .env trouvé" -ForegroundColor Green
}

# Construction des images Docker
Write-Host "`n🏗️  Construction des images Docker..." -ForegroundColor Cyan
docker-compose build
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Erreur lors de la construction des images" -ForegroundColor Red
    exit 1
}
Write-Host "✅ Images construites avec succès" -ForegroundColor Green

# Démarrage des conteneurs
Write-Host "`n🚀 Démarrage des conteneurs..." -ForegroundColor Cyan
docker-compose up -d
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Erreur lors du démarrage des conteneurs" -ForegroundColor Red
    exit 1
}
Write-Host "✅ Conteneurs démarrés" -ForegroundColor Green

# Attendre que MySQL soit prêt
Write-Host "`n⏳ Attente que MySQL soit prêt..." -ForegroundColor Yellow
$maxAttempts = 30
$attempt = 0
do {
    $attempt++
    Start-Sleep -Seconds 2
    $mysqlReady = docker-compose exec -T db mysqladmin ping -h localhost -u root -p"$(Get-Content .env | Select-String 'DB_ROOT_PASSWORD' | ForEach-Object { $_ -replace 'DB_ROOT_PASSWORD=', '' })" 2>&1 | Select-String "mysqld is alive"
    if ($mysqlReady) {
        Write-Host "✅ MySQL est prêt" -ForegroundColor Green
        break
    }
    Write-Host "  Tentative $attempt/$maxAttempts..." -ForegroundColor Gray
} while ($attempt -lt $maxAttempts)

if (-not $mysqlReady) {
    Write-Host "❌ MySQL n'a pas démarré correctement" -ForegroundColor Red
    Write-Host "Vérifiez les logs avec: docker-compose logs db" -ForegroundColor Yellow
    exit 1
}

# Vérifier si APP_KEY est défini
Write-Host "`n🔑 Vérification de la clé d'application..." -ForegroundColor Yellow
$appKey = Get-Content .env | Select-String "^APP_KEY=" | ForEach-Object { $_ -replace "APP_KEY=", "" }
if (-not $appKey -or $appKey -eq "") {
    Write-Host "📝 Génération de la clé d'application..." -ForegroundColor Cyan
    docker-compose exec -T app php artisan key:generate
    Write-Host "✅ Clé d'application générée" -ForegroundColor Green
} else {
    Write-Host "✅ Clé d'application déjà définie" -ForegroundColor Green
}

# Exécuter les migrations
Write-Host "`n🗄️  Exécution des migrations..." -ForegroundColor Cyan
docker-compose exec -T app php artisan migrate --force
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Migrations exécutées avec succès" -ForegroundColor Green
} else {
    Write-Host "⚠️  Erreur lors des migrations (peut être normal si déjà exécutées)" -ForegroundColor Yellow
}

# Créer le lien symbolique pour le storage
Write-Host "`n📂 Création du lien symbolique pour le storage..." -ForegroundColor Cyan
docker-compose exec -T app php artisan storage:link
Write-Host "✅ Lien symbolique créé" -ForegroundColor Green

# Optimiser l'application
Write-Host "`n⚡ Optimisation de l'application..." -ForegroundColor Cyan
docker-compose exec -T app php artisan optimize
Write-Host "✅ Application optimisée" -ForegroundColor Green

# Afficher les informations finales
Write-Host "`n" -NoNewline
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "🎉 Installation terminée avec succès!" -ForegroundColor Green
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "`n📍 Accès aux services:" -ForegroundColor Yellow
Write-Host "   🌐 Application:  " -NoNewline -ForegroundColor White
Write-Host "http://localhost:8000" -ForegroundColor Cyan
Write-Host "   🗄️  phpMyAdmin:  " -NoNewline -ForegroundColor White
Write-Host "http://localhost:8080" -ForegroundColor Cyan
Write-Host "`n📝 Commandes utiles:" -ForegroundColor Yellow
Write-Host "   docker-compose logs -f        " -NoNewline -ForegroundColor White
Write-Host "# Voir les logs" -ForegroundColor Gray
Write-Host "   docker-compose down           " -NoNewline -ForegroundColor White
Write-Host "# Arrêter les conteneurs" -ForegroundColor Gray
Write-Host "   docker-compose exec app bash  " -NoNewline -ForegroundColor White
Write-Host "# Accéder au conteneur" -ForegroundColor Gray
Write-Host "`n🚀 Bon développement!" -ForegroundColor Green
Write-Host "═══════════════════════════════════════════════════════════`n" -ForegroundColor Cyan

