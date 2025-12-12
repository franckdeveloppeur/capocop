# Script PowerShell simplifié pour construire et démarrer Docker
# Usage: .\docker-build-simple.ps1

$ErrorActionPreference = "Stop"

Write-Host "🐳 Construction et démarrage de Capocop avec Docker..." -ForegroundColor Cyan

# Vérifier Docker
Write-Host "`n📦 Vérification de Docker..." -ForegroundColor Yellow
try {
    docker info > $null 2>&1
    Write-Host "✅ Docker est opérationnel" -ForegroundColor Green
} catch {
    Write-Host "❌ Docker n'est pas en cours d'exécution. Veuillez démarrer Docker Desktop." -ForegroundColor Red
    exit 1
}

# Créer le fichier .env si nécessaire
Write-Host "`n🔍 Vérification du fichier .env..." -ForegroundColor Yellow
if (-not (Test-Path ".env")) {
    Write-Host "📋 Création du fichier .env..." -ForegroundColor Cyan
    Copy-Item "docker/env-template.txt" ".env"
    
    # Générer des mots de passe aléatoires sécurisés
    Add-Type -AssemblyName System.Web
    $dbPassword = [System.Web.Security.Membership]::GeneratePassword(16, 4)
    $dbRootPassword = [System.Web.Security.Membership]::GeneratePassword(16, 4)
    $redisPassword = [System.Web.Security.Membership]::GeneratePassword(16, 4)
    
    # Remplacer dans .env
    (Get-Content ".env") -replace "DB_PASSWORD=secret_password", "DB_PASSWORD=$dbPassword" `
        -replace "DB_ROOT_PASSWORD=root_password", "DB_ROOT_PASSWORD=$dbRootPassword" `
        -replace "REDIS_PASSWORD=redis_password", "REDIS_PASSWORD=$redisPassword" | 
        Set-Content ".env"
    
    Write-Host "✅ Fichier .env créé avec mots de passe sécurisés" -ForegroundColor Green
} else {
    Write-Host "✅ Fichier .env existe déjà" -ForegroundColor Green
}

# Arrêter et supprimer les anciens conteneurs
Write-Host "`n🧹 Nettoyage des anciens conteneurs..." -ForegroundColor Yellow
docker-compose down -v 2>$null

# Construction sans cache pour éviter les problèmes
Write-Host "`n🏗️  Construction de l'image Docker..." -ForegroundColor Cyan
Write-Host "⚠️  Cela peut prendre 5-10 minutes la première fois..." -ForegroundColor Yellow

docker-compose build --no-cache

if ($LASTEXITCODE -ne 0) {
    Write-Host "`n❌ Erreur lors de la construction" -ForegroundColor Red
    Write-Host "`n💡 Solutions possibles:" -ForegroundColor Yellow
    Write-Host "   1. Vérifiez votre connexion Internet" -ForegroundColor White
    Write-Host "   2. Vérifiez que Docker a assez de mémoire (minimum 4 GB)" -ForegroundColor White
    Write-Host "   3. Essayez: docker system prune -a" -ForegroundColor White
    exit 1
}

Write-Host "✅ Image construite avec succès" -ForegroundColor Green

# Démarrage
Write-Host "`n🚀 Démarrage des conteneurs..." -ForegroundColor Cyan
docker-compose up -d

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Erreur lors du démarrage" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Conteneurs démarrés" -ForegroundColor Green

# Attendre MySQL
Write-Host "`n⏳ Attente de MySQL (30 secondes)..." -ForegroundColor Yellow
Start-Sleep -Seconds 30

# Configuration Laravel
Write-Host "`n⚙️  Configuration de Laravel..." -ForegroundColor Cyan

Write-Host "📝 Génération de la clé..." -ForegroundColor Yellow
docker-compose exec -T app php artisan key:generate --force

Write-Host "🗄️  Exécution des migrations..." -ForegroundColor Yellow
docker-compose exec -T app php artisan migrate --force 2>$null

Write-Host "📂 Création du lien storage..." -ForegroundColor Yellow
docker-compose exec -T app php artisan storage:link 2>$null

Write-Host "⚡ Optimisation..." -ForegroundColor Yellow
docker-compose exec -T app php artisan optimize

# Résultat final
Write-Host "`n" -NoNewline
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "🎉 Installation terminée!" -ForegroundColor Green
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "`n📍 Accès aux services:" -ForegroundColor Yellow
Write-Host "   🌐 Application:  " -NoNewline
Write-Host "http://localhost:8000" -ForegroundColor Cyan
Write-Host "   🗄️  phpMyAdmin:  " -NoNewline
Write-Host "http://localhost:8080" -ForegroundColor Cyan
Write-Host "`n📝 Commandes utiles:" -ForegroundColor Yellow
Write-Host "   docker-compose logs -f app    " -NoNewline
Write-Host "# Voir les logs" -ForegroundColor Gray
Write-Host "   docker-compose down           " -NoNewline
Write-Host "# Arrêter" -ForegroundColor Gray
Write-Host "   docker-compose exec app bash  " -NoNewline
Write-Host "# Shell" -ForegroundColor Gray
Write-Host "`n🚀 L'application est prête!" -ForegroundColor Green
Write-Host "═══════════════════════════════════════════════════════════`n" -ForegroundColor Cyan



