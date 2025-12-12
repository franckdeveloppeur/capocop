#!/bin/bash
# Script Bash simplifié pour construire et démarrer Docker
# Usage: ./docker-build-simple.sh

set -e

echo "🐳 Construction et démarrage de Capocop avec Docker..."

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Vérifier Docker
echo -e "\n${YELLOW}📦 Vérification de Docker...${NC}"
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}❌ Docker n'est pas en cours d'exécution${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Docker est opérationnel${NC}"

# Créer le fichier .env si nécessaire
echo -e "\n${YELLOW}🔍 Vérification du fichier .env...${NC}"
if [ ! -f .env ]; then
    echo -e "${CYAN}📋 Création du fichier .env...${NC}"
    cp docker/env-template.txt .env
    
    # Générer des mots de passe aléatoires
    DB_PASSWORD=$(openssl rand -base64 16 | tr -dc 'a-zA-Z0-9' | head -c 16)
    DB_ROOT_PASSWORD=$(openssl rand -base64 16 | tr -dc 'a-zA-Z0-9' | head -c 16)
    REDIS_PASSWORD=$(openssl rand -base64 16 | tr -dc 'a-zA-Z0-9' | head -c 16)
    
    # Remplacer dans .env
    sed -i.bak "s/DB_PASSWORD=secret_password/DB_PASSWORD=$DB_PASSWORD/" .env
    sed -i.bak "s/DB_ROOT_PASSWORD=root_password/DB_ROOT_PASSWORD=$DB_ROOT_PASSWORD/" .env
    sed -i.bak "s/REDIS_PASSWORD=redis_password/REDIS_PASSWORD=$REDIS_PASSWORD/" .env
    rm -f .env.bak
    
    echo -e "${GREEN}✅ Fichier .env créé avec mots de passe sécurisés${NC}"
else
    echo -e "${GREEN}✅ Fichier .env existe déjà${NC}"
fi

# Arrêter et supprimer les anciens conteneurs
echo -e "\n${YELLOW}🧹 Nettoyage des anciens conteneurs...${NC}"
docker-compose down -v 2>/dev/null || true

# Construction sans cache
echo -e "\n${CYAN}🏗️  Construction de l'image Docker...${NC}"
echo -e "${YELLOW}⚠️  Cela peut prendre 5-10 minutes la première fois...${NC}"

if ! docker-compose build --no-cache; then
    echo -e "\n${RED}❌ Erreur lors de la construction${NC}"
    echo -e "\n${YELLOW}💡 Solutions possibles:${NC}"
    echo "   1. Vérifiez votre connexion Internet"
    echo "   2. Vérifiez que Docker a assez de mémoire (minimum 4 GB)"
    echo "   3. Essayez: docker system prune -a"
    exit 1
fi

echo -e "${GREEN}✅ Image construite avec succès${NC}"

# Démarrage
echo -e "\n${CYAN}🚀 Démarrage des conteneurs...${NC}"
if ! docker-compose up -d; then
    echo -e "${RED}❌ Erreur lors du démarrage${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Conteneurs démarrés${NC}"

# Attendre MySQL
echo -e "\n${YELLOW}⏳ Attente de MySQL (30 secondes)...${NC}"
sleep 30

# Configuration Laravel
echo -e "\n${CYAN}⚙️  Configuration de Laravel...${NC}"

echo -e "${YELLOW}📝 Génération de la clé...${NC}"
docker-compose exec -T app php artisan key:generate --force

echo -e "${YELLOW}🗄️  Exécution des migrations...${NC}"
docker-compose exec -T app php artisan migrate --force 2>/dev/null || true

echo -e "${YELLOW}📂 Création du lien storage...${NC}"
docker-compose exec -T app php artisan storage:link 2>/dev/null || true

echo -e "${YELLOW}⚡ Optimisation...${NC}"
docker-compose exec -T app php artisan optimize

# Résultat final
echo -e "\n${CYAN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}🎉 Installation terminée!${NC}"
echo -e "${CYAN}═══════════════════════════════════════════════════════════${NC}"
echo -e "\n${YELLOW}📍 Accès aux services:${NC}"
echo -e "   🌐 Application:  ${CYAN}http://localhost:8000${NC}"
echo -e "   🗄️  phpMyAdmin:  ${CYAN}http://localhost:8080${NC}"
echo -e "\n${YELLOW}📝 Commandes utiles:${NC}"
echo "   docker-compose logs -f app    # Voir les logs"
echo "   docker-compose down           # Arrêter"
echo "   docker-compose exec app bash  # Shell"
echo -e "\n${GREEN}🚀 L'application est prête!${NC}"
echo -e "${CYAN}═══════════════════════════════════════════════════════════${NC}\n"



