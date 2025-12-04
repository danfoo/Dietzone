#!/bin/bash

##############################################################################
# Script de test pour la notification de dîner - app.dietsenegal.net
# Usage: bash test_dinner_notification.sh
##############################################################################

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}Test Notification Dîner 18h23${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Configuration du serveur
PHP_PATH="/usr/bin/php"
PROJECT_PATH="/home/trpuftja/app"
CRON_COMMAND="$PHP_PATH $PROJECT_PATH/index.php cron/index"

echo -e "${GREEN}Configuration détectée:${NC}"
echo "  PHP: $PHP_PATH"
echo "  Projet: $PROJECT_PATH"
echo ""

# 1. Vérifier que PHP existe
echo -e "${BLUE}1️⃣  Vérification de PHP...${NC}"
if [ -f "$PHP_PATH" ]; then
    echo -e "${GREEN}✓ PHP trouvé${NC}"
    PHP_VERSION=$($PHP_PATH -v | head -n 1)
    echo "  Version: $PHP_VERSION"
else
    echo -e "${RED}✗ PHP non trouvé à $PHP_PATH${NC}"
    exit 1
fi
echo ""

# 2. Vérifier que le projet existe
echo -e "${BLUE}2️⃣  Vérification du projet...${NC}"
if [ -f "$PROJECT_PATH/index.php" ]; then
    echo -e "${GREEN}✓ Perfex CRM trouvé${NC}"
else
    echo -e "${RED}✗ Perfex CRM non trouvé à $PROJECT_PATH${NC}"
    exit 1
fi
echo ""

# 3. Vérifier le cron
echo -e "${BLUE}3️⃣  Vérification du cron...${NC}"
if command -v crontab &> /dev/null; then
    CRON_CONFIG=$(crontab -l 2>/dev/null | grep "cron/index")
    if [ -n "$CRON_CONFIG" ]; then
        echo -e "${GREEN}✓ Cron configuré${NC}"
        echo "  $CRON_CONFIG"

        # Vérifier la fréquence
        if echo "$CRON_CONFIG" | grep -q "^\*/5"; then
            echo -e "${GREEN}  ✓ Fréquence correcte (toutes les 5 minutes)${NC}"
        else
            echo -e "${YELLOW}  ⚠️  Fréquence inhabituelle${NC}"
            echo -e "${YELLOW}  Recommandé: */5 * * * * ...${NC}"
        fi
    else
        echo -e "${RED}✗ Cron non configuré${NC}"
    fi
else
    echo -e "${YELLOW}⚠️  Commande crontab non disponible${NC}"
    echo "  Vérifiez via votre panel (cPanel/Plesk)"
fi
echo ""

# 4. Test d'exécution manuelle
echo -e "${BLUE}4️⃣  Test d'exécution manuelle...${NC}"
echo -e "${YELLOW}Exécution du cron (peut prendre quelques secondes)...${NC}"
echo ""

OUTPUT=$($CRON_COMMAND 2>&1)
EXIT_CODE=$?

if [ $EXIT_CODE -eq 0 ]; then
    echo -e "${GREEN}✓ Cron exécuté avec succès${NC}"

    # Vérifier si des notifications Dietetic ont été mentionnées
    if echo "$OUTPUT" | grep -qi "dietetic\|notification"; then
        echo -e "${GREEN}✓ Module Dietetic détecté dans l'exécution${NC}"
    else
        echo -e "${YELLOW}ℹ  Aucune activité Dietetic visible${NC}"
    fi
else
    echo -e "${RED}✗ Erreur lors de l'exécution (code: $EXIT_CODE)${NC}"
    echo ""
    echo -e "${RED}Sortie de l'erreur:${NC}"
    echo "$OUTPUT"
fi
echo ""

# 5. Vérifier l'heure actuelle
echo -e "${BLUE}5️⃣  Informations temporelles...${NC}"
CURRENT_TIME=$(date '+%H:%M')
CURRENT_DATE=$(date '+%Y-%m-%d %H:%M:%S')
echo "  Heure actuelle: $CURRENT_TIME"
echo "  Date complète: $CURRENT_DATE"

# Calculer quand sera le prochain passage du cron
CURRENT_MINUTE=$(date '+%M')
NEXT_MINUTE=$((($CURRENT_MINUTE / 5 + 1) * 5))
if [ $NEXT_MINUTE -ge 60 ]; then
    NEXT_MINUTE=$((NEXT_MINUTE - 60))
    NEXT_HOUR=$(($(date '+%H') + 1))
else
    NEXT_HOUR=$(date '+%H')
fi

printf "  Prochain cron: %02d:%02d (dans environ %d minutes)\n" $NEXT_HOUR $NEXT_MINUTE $(((NEXT_MINUTE - CURRENT_MINUTE + 60) % 60))
echo ""

# 6. Vérifications à faire dans Perfex
echo -e "${BLUE}6️⃣  Vérifications à faire dans Perfex CRM...${NC}"
echo ""
echo -e "${YELLOW}Connectez-vous à https://app.dietsenegal.net/admin${NC}"
echo ""
echo "A. Vérifier le cron Perfex:"
echo "   → Setup > Settings > Cron Job"
echo "   → \"Last Cron Run\" doit être récent (< 5 min)"
echo ""
echo "B. Vérifier les logs:"
echo "   → Admin > Activity Log"
echo "   → Rechercher: \"Dietetic Cron\""
echo "   → Vous devriez voir: \"Dietetic Cron: X notifications envoyées\""
echo ""
echo "C. Vérifier vos préférences:"
echo "   → https://app.dietsenegal.net/dietetic/portal/notification_preferences"
echo "   → Rappel Dîner: COCHÉ"
echo "   → Heure: 18:23"
echo "   → Au moins 1 canal activé (Email/SMS/WhatsApp)"
echo ""
echo "D. Vérifier les logs des notifications:"
echo "   → Admin > Dietetic > Notifications > Logs"
echo "   → Cherchez votre notification de dîner"
echo ""

# 7. Résumé
echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}RÉSUMÉ${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

if [ $EXIT_CODE -eq 0 ]; then
    echo -e "${GREEN}✅ Le système fonctionne correctement${NC}"
    echo ""
    echo "Si vous n'avez pas reçu la notification, vérifiez:"
    echo "  1. Vos préférences sont bien sauvegardées"
    echo "  2. L'heure du dîner est bien 18:23"
    echo "  3. Au moins un canal est activé"
    echo "  4. Votre email/téléphone est renseigné"
    echo "  5. La configuration Email/SMS/WhatsApp dans Admin"
    echo ""
    echo "La prochaine notification sera envoyée au prochain passage"
    echo "du cron après 18:23 (± 5 minutes maximum)"
else
    echo -e "${RED}❌ Des problèmes ont été détectés${NC}"
    echo ""
    echo "Actions recommandées:"
    echo "  1. Vérifiez les erreurs ci-dessus"
    echo "  2. Consultez les logs: $PROJECT_PATH/application/logs/"
    echo "  3. Vérifiez la configuration de la base de données"
    echo "  4. Contactez le support si le problème persiste"
fi
echo ""

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}Documentation complète:${NC}"
echo "  modules/dietetic/VERIFICATION_SERVEUR.md"
echo "  modules/dietetic/TROUBLESHOOTING.md"
echo -e "${BLUE}========================================${NC}"
