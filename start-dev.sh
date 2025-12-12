#!/bin/bash
# Script de démarrage simplifié pour développement
set -e

# Couleurs
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

echo -e "${CYAN}🐳 Démarrage de Capocop (Mode Développement)${NC}\n"

# 1. Créer .env si nécessaire
if [ ! -f .env ]; then
    echo -e "${YELLOW}📋 Création du fichier .env...${NC}"
    cp docker/env-template.txt .env
    echo -e "${GREEN}✅ Fichier .env créé${NC}\n"
fi

# 2. Construire l'image (rapide car pas d'installation de dépendances)
echo -e "${CYAN}🏗️  Construction de l'image...${NC}"
docker-compose -f docker-compose-dev.yml build

# 3. Démarrer les conteneurs
echo -e "${CYAN}🚀 Démarrage des conteneurs...${NC}"
docker-compose -f docker-compose-dev.yml up -d

# 4. Attendre MySQL
echo -e "${YELLOW}⏳ Attente de MySQL (20 secondes)...${NC}"
sleep 20

# 5. Installer les dépendances dans le conteneur
echo -e "${CYAN}📦 Installation des dépendances...${NC}"
echo -e "${YELLOW}⚠️  Cela peut prendre 5-10 minutes...${NC}"
docker-compose -f docker-compose-dev.yml exec -T app composer install --no-interaction --prefer-dist --no-plugins --no-scripts
docker-compose -f docker-compose-dev.yml exec -T app composer dump-autoload

# 6. Installer les dépendances NPM
echo -e "${CYAN}📦 Installation de NPM...${NC}"
docker-compose -f docker-compose-dev.yml exec -T app npm install

# 7. Compiler les assets
echo -e "${CYAN}🎨 Compilation des assets...${NC}"
docker-compose -f docker-compose-dev.yml exec -T app npm run build

# 8. Configuration Laravel
echo -e "${CYAN}⚙️  Configuration Laravel...${NC}"
docker-compose -f docker-compose-dev.yml exec -T app php artisan key:generate --force
docker-compose -f docker-compose-dev.yml exec -T app php artisan migrate --force
docker-compose -f docker-compose-dev.yml exec -T app php artisan storage:link
docker-compose -f docker-compose-dev.yml exec -T app php artisan optimize

# Résultat
echo -e "\n${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}🎉 Application prête!${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "\n${YELLOW}📍 Accès:${NC}"
echo -e "   🌐 Application:  ${CYAN}http://localhost:8000${NC}"
echo -e "   🗄️  phpMyAdmin:  ${CYAN}http://localhost:8080${NC}"
echo -e "\n${YELLOW}📝 Commandes utiles:${NC}"
echo "   docker-compose -f docker-compose-dev.yml logs -f"
echo "   docker-compose -f docker-compose-dev.yml down"
echo "   docker-compose -f docker-compose-dev.yml exec app bash"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}\n"



