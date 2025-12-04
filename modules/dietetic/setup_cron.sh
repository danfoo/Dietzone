#!/bin/bash

##############################################################################
# Script d'installation du Cron pour Dietetic Notifications
# Ce script aide à configurer automatiquement le cron pour Perfex CRM
##############################################################################

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}Installation du Cron Dietetic${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    echo -e "${YELLOW}⚠️  Attention: Vous exécutez ce script en tant que root${NC}"
    echo -e "${YELLOW}   Le cron sera configuré pour l'utilisateur root${NC}"
    read -p "Continuer? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

echo -e "${GREEN}📂 Répertoire du projet détecté:${NC}"
echo "   $PROJECT_ROOT"
echo ""

# Check if Perfex exists
if [ ! -f "$PROJECT_ROOT/index.php" ]; then
    echo -e "${RED}❌ Erreur: index.php non trouvé${NC}"
    echo "   Assurez-vous d'être dans le bon répertoire"
    exit 1
fi

echo -e "${GREEN}✓ Perfex CRM détecté${NC}"
echo ""

# PHP path detection
echo -e "${BLUE}🔍 Détection de PHP...${NC}"

PHP_PATH=$(which php)
if [ -z "$PHP_PATH" ]; then
    echo -e "${RED}❌ PHP non trouvé dans PATH${NC}"
    echo "   Veuillez installer PHP ou spécifier le chemin manuellement"
    exit 1
fi

PHP_VERSION=$($PHP_PATH -v | head -n 1)
echo -e "${GREEN}✓ PHP trouvé: $PHP_PATH${NC}"
echo "   Version: $PHP_VERSION"
echo ""

# Cron command
CRON_COMMAND="*/5 * * * * $PHP_PATH $PROJECT_ROOT/index.php cron/index"

echo -e "${BLUE}📝 Commande cron à ajouter:${NC}"
echo -e "${YELLOW}$CRON_COMMAND${NC}"
echo ""

# Check if cron already exists
EXISTING_CRON=$(crontab -l 2>/dev/null || echo "")

if echo "$EXISTING_CRON" | grep -q "$PROJECT_ROOT/index.php cron/index"; then
    echo -e "${YELLOW}⚠️  Un cron existe déjà pour ce projet${NC}"
    echo ""
    echo "Cron existant:"
    echo "$EXISTING_CRON" | grep "$PROJECT_ROOT/index.php cron/index"
    echo ""
    read -p "Voulez-vous le remplacer? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${BLUE}Installation annulée${NC}"
        exit 0
    fi

    # Remove existing cron
    TEMP_CRON=$(echo "$EXISTING_CRON" | grep -v "$PROJECT_ROOT/index.php cron/index")
    echo "$TEMP_CRON" | crontab -
    echo -e "${GREEN}✓ Ancien cron supprimé${NC}"
fi

# Add new cron
(crontab -l 2>/dev/null || echo ""; echo "$CRON_COMMAND") | crontab -

echo -e "${GREEN}✅ Cron installé avec succès!${NC}"
echo ""

# Verify installation
echo -e "${BLUE}🔍 Vérification...${NC}"
NEW_CRON=$(crontab -l 2>/dev/null)

if echo "$NEW_CRON" | grep -q "$PROJECT_ROOT/index.php cron/index"; then
    echo -e "${GREEN}✓ Cron vérifié et actif${NC}"
    echo ""
    echo "Le cron s'exécutera toutes les 5 minutes"
else
    echo -e "${RED}❌ Erreur: Le cron n'a pas été ajouté correctement${NC}"
    exit 1
fi

# Test cron manually
echo ""
echo -e "${BLUE}🧪 Test du cron...${NC}"
read -p "Voulez-vous tester le cron maintenant? (y/n) " -n 1 -r
echo

if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    echo -e "${YELLOW}Exécution du cron...${NC}"
    echo ""
    $PHP_PATH $PROJECT_ROOT/index.php cron/index
    echo ""
    echo -e "${GREEN}✓ Test terminé${NC}"
    echo ""
    echo "Consultez les logs dans:"
    echo "  Admin > Activity Log (rechercher 'Dietetic Cron')"
fi

echo ""
echo -e "${BLUE}========================================${NC}"
echo -e "${GREEN}Installation terminée!${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""
echo "Prochaines étapes:"
echo "  1. Vérifiez que le cron s'exécute: crontab -l"
echo "  2. Consultez les logs: Admin > Activity Log"
echo "  3. Testez les notifications: Admin > Dietetic > Notifications"
echo ""
echo "Pour désinstaller le cron:"
echo "  crontab -e"
echo "  Puis supprimez la ligne contenant: $PROJECT_ROOT/index.php"
echo ""
echo -e "${GREEN}Documentation complète: modules/dietetic/CRON_SETUP.md${NC}"
echo ""
