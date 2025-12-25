#!/bin/bash

# ================================================================
# Script d'Application du Fix PayPal Session Loss
# ================================================================

echo "🔧 Application du fix PayPal Session Loss..."
echo ""

# Couleurs
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Vérifier que le fichier de migration existe
if [ ! -f "modules/dietetic/migrations/fix_paypal_session_issue.sql" ]; then
    echo -e "${RED}❌ Fichier de migration introuvable!${NC}"
    exit 1
fi

echo -e "${YELLOW}📋 Informations requises pour la base de données:${NC}"
read -p "Host MySQL (localhost): " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "Nom de la base de données: " DB_NAME
if [ -z "$DB_NAME" ]; then
    echo -e "${RED}❌ Nom de base de données requis!${NC}"
    exit 1
fi

read -p "Utilisateur MySQL: " DB_USER
if [ -z "$DB_USER" ]; then
    echo -e "${RED}❌ Utilisateur MySQL requis!${NC}"
    exit 1
fi

read -sp "Mot de passe MySQL: " DB_PASS
echo ""

# Remplacer le préfixe de table (tbldietic_ → votre préfixe)
read -p "Préfixe des tables Perfex (tbl): " DB_PREFIX
DB_PREFIX=${DB_PREFIX:-tbl}

echo ""
echo -e "${YELLOW}🔄 Application de la migration...${NC}"

# Créer un fichier temporaire avec le bon préfixe
TMP_FILE=$(mktemp)
sed "s/tbldietic_/${DB_PREFIX}dietic_/g" modules/dietetic/migrations/fix_paypal_session_issue.sql > "$TMP_FILE"

# Appliquer la migration
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$TMP_FILE" 2>&1

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Migration appliquée avec succès!${NC}"
    echo ""

    # Vérifier que la table a été créée
    TABLE_EXISTS=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -s -N -e "SHOW TABLES LIKE '${DB_PREFIX}dietic_payment_tokens'")

    if [ -n "$TABLE_EXISTS" ]; then
        echo -e "${GREEN}✅ Table ${DB_PREFIX}dietic_payment_tokens créée avec succès${NC}"
        echo ""
        echo "📊 Structure de la table:"
        mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "DESC ${DB_PREFIX}dietic_payment_tokens"
    else
        echo -e "${RED}❌ La table n'a pas été créée${NC}"
        exit 1
    fi
else
    echo -e "${RED}❌ Erreur lors de l'application de la migration${NC}"
    rm "$TMP_FILE"
    exit 1
fi

# Nettoyer
rm "$TMP_FILE"

echo ""
echo -e "${GREEN}✨ Fix PayPal Session Loss appliqué avec succès!${NC}"
echo ""
echo "📝 Prochaines étapes:"
echo "  1. Tester un paiement PayPal"
echo "  2. Vérifier les logs: SELECT * FROM ${DB_PREFIX}logs WHERE description LIKE '%PAYMENT TOKEN%'"
echo "  3. Monitorer: SELECT * FROM ${DB_PREFIX}dietic_payment_tokens"
echo ""
echo "📖 Documentation complète: PAYPAL_SESSION_FIX.md"
