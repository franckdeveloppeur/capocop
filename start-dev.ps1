# Script de démarrage simplifié pour développement (PowerShell)

Write-Host "🐳 Démarrage de Capocop (Mode Développement)`n" -ForegroundColor Cyan

# 1. Créer .env si nécessaire
if (-not (Test-Path ".env")) {
    Write-Host "📋 Création du fichier .env..." -ForegroundColor Yellow
    Copy-Item "docker/env-template.txt" ".env"
    Write-Host "✅ Fichier .env créé`n" -ForegroundColor Green
}

# 2. Construire l'image
Write-Host "🏗️  Construction de l'image..." -ForegroundColor Cyan
docker-compose -f docker-compose-dev.yml build

# 3. Démarrer les conteneurs
Write-Host "🚀 Démarrage des conteneurs..." -ForegroundColor Cyan
docker-compose -f docker-compose-dev.yml up -d

# 4. Attendre MySQL
Write-Host "⏳ Attente de MySQL (20 secondes)..." -ForegroundColor Yellow
Start-Sleep -Seconds 20

# 5. Installer les dépendances
Write-Host "📦 Installation des dépendances PHP..." -ForegroundColor Cyan
docker-compose -f docker-compose-dev.yml exec -T app composer install --no-interaction

# 6. Installer NPM
Write-Host "📦 Installation de NPM..." -ForegroundColor Cyan
docker-compose -f docker-compose-dev.yml exec -T app npm install

# 7. Compiler les assets
Write-Host "🎨 Compilation des assets..." -ForegroundColor Cyan
docker-compose -f docker-compose-dev.yml exec -T app npm run build

# 8. Configuration Laravel
Write-Host "⚙️  Configuration Laravel..." -ForegroundColor Cyan
docker-compose -f docker-compose-dev.yml exec -T app php artisan key:generate --force
docker-compose -f docker-compose-dev.yml exec -T app php artisan migrate --force
docker-compose -f docker-compose-dev.yml exec -T app php artisan storage:link
docker-compose -f docker-compose-dev.yml exec -T app php artisan optimize

# Résultat
Write-Host "`n═══════════════════════════════════════════════════════════" -ForegroundColor Green
Write-Host "🎉 Application prête!" -ForegroundColor Green
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Green
Write-Host "`n📍 Accès:" -ForegroundColor Yellow
Write-Host "   🌐 Application:  " -NoNewline
Write-Host "http://localhost:8000" -ForegroundColor Cyan
Write-Host "   🗄️  phpMyAdmin:  " -NoNewline
Write-Host "http://localhost:8080" -ForegroundColor Cyan
Write-Host "`n📝 Commandes utiles:" -ForegroundColor Yellow
Write-Host "   docker-compose -f docker-compose-dev.yml logs -f"
Write-Host "   docker-compose -f docker-compose-dev.yml down"
Write-Host "   docker-compose -f docker-compose-dev.yml exec app bash"
Write-Host "═══════════════════════════════════════════════════════════`n" -ForegroundColor Green



