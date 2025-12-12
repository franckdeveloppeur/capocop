#!/bin/bash
# Script Bash pour démarrer le projet Docker Capocop (Linux/Mac)
# Usage: ./docker-start.sh

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${CYAN}🐳 Démarrage du projet Capocop avec Docker...${NC}"

# Vérifier si Docker est en cours d'exécution
echo -e "\n${YELLOW}📦 Vérification de Docker...${NC}"
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}❌ Docker n'est pas en cours d'exécution. Veuillez démarrer Docker.${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Docker est opérationnel${NC}"

# Vérifier si le fichier .env existe
echo -e "\n${YELLOW}🔍 Vérification du fichier .env...${NC}"
if [ ! -f .env ]; then
    echo -e "${YELLOW}⚠️  Le fichier .env n'existe pas.${NC}"
    echo -e "${CYAN}📋 Création du fichier .env depuis le template...${NC}"
    cp docker/env-template.txt .env
    echo -e "${GREEN}✅ Fichier .env créé. Veuillez modifier les mots de passe avant de continuer!${NC}"
    echo -e "\n${RED}⚠️  IMPORTANT: Éditez le fichier .env et changez au minimum:${NC}"
    echo -e "${YELLOW}   - DB_PASSWORD${NC}"
    echo -e "${YELLOW}   - DB_ROOT_PASSWORD${NC}"
    echo -e "${YELLOW}   - REDIS_PASSWORD${NC}"
    echo -e "\nAppuyez sur Entrée une fois que vous avez modifié le fichier .env..."
    read -r
fi
echo -e "${GREEN}✅ Fichier .env trouvé${NC}"

# Construction des images Docker
echo -e "\n${CYAN}🏗️  Construction des images Docker...${NC}"
if ! docker-compose build; then
    echo -e "${RED}❌ Erreur lors de la construction des images${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Images construites avec succès${NC}"

# Démarrage des conteneurs
echo -e "\n${CYAN}🚀 Démarrage des conteneurs...${NC}"
if ! docker-compose up -d; then
    echo -e "${RED}❌ Erreur lors du démarrage des conteneurs${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Conteneurs démarrés${NC}"

# Attendre que MySQL soit prêt
echo -e "\n${YELLOW}⏳ Attente que MySQL soit prêt...${NC}"
MAX_ATTEMPTS=30
ATTEMPT=0
DB_ROOT_PASSWORD=$(grep "^DB_ROOT_PASSWORD=" .env | cut -d '=' -f2)

while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
    ATTEMPT=$((ATTEMPT + 1))
    sleep 2
    if docker-compose exec -T db mysqladmin ping -h localhost -u root -p"$DB_ROOT_PASSWORD" > /dev/null 2>&1; then
        echo -e "${GREEN}✅ MySQL est prêt${NC}"
        break
    fi
    echo -e "  Tentative $ATTEMPT/$MAX_ATTEMPTS..."
done

if [ $ATTEMPT -eq $MAX_ATTEMPTS ]; then
    echo -e "${RED}❌ MySQL n'a pas démarré correctement${NC}"
    echo -e "${YELLOW}Vérifiez les logs avec: docker-compose logs db${NC}"
    exit 1
fi

# Vérifier si APP_KEY est défini
echo -e "\n${YELLOW}🔑 Vérification de la clé d'application...${NC}"
APP_KEY=$(grep "^APP_KEY=" .env | cut -d '=' -f2)
if [ -z "$APP_KEY" ]; then
    echo -e "${CYAN}📝 Génération de la clé d'application...${NC}"
    docker-compose exec -T app php artisan key:generate
    echo -e "${GREEN}✅ Clé d'application générée${NC}"
else
    echo -e "${GREEN}✅ Clé d'application déjà définie${NC}"
fi

# Exécuter les migrations
echo -e "\n${CYAN}🗄️  Exécution des migrations...${NC}"
if docker-compose exec -T app php artisan migrate --force; then
    echo -e "${GREEN}✅ Migrations exécutées avec succès${NC}"
else
    echo -e "${YELLOW}⚠️  Erreur lors des migrations (peut être normal si déjà exécutées)${NC}"
fi

# Créer le lien symbolique pour le storage
echo -e "\n${CYAN}📂 Création du lien symbolique pour le storage...${NC}"
docker-compose exec -T app php artisan storage:link
echo -e "${GREEN}✅ Lien symbolique créé${NC}"

# Optimiser l'application
echo -e "\n${CYAN}⚡ Optimisation de l'application...${NC}"
docker-compose exec -T app php artisan optimize
echo -e "${GREEN}✅ Application optimisée${NC}"

# Afficher les informations finales
echo -e "\n${CYAN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}🎉 Installation terminée avec succès!${NC}"
echo -e "${CYAN}═══════════════════════════════════════════════════════════${NC}"
echo -e "\n${YELLOW}📍 Accès aux services:${NC}"
echo -e "   🌐 Application:  ${CYAN}http://localhost:8000${NC}"
echo -e "   🗄️  phpMyAdmin:  ${CYAN}http://localhost:8080${NC}"
echo -e "\n${YELLOW}📝 Commandes utiles:${NC}"
echo -e "   docker-compose logs -f        # Voir les logs"
echo -e "   docker-compose down           # Arrêter les conteneurs"
echo -e "   docker-compose exec app bash  # Accéder au conteneur"
echo -e "\n${GREEN}🚀 Bon développement!${NC}"
echo -e "${CYAN}═══════════════════════════════════════════════════════════${NC}\n"

