#!/bin/bash

# Script pour installer le système de notifications
# Exécuter: bash install_notifications.sh

echo "=========================================="
echo "Installation Système de Notifications"
echo "=========================================="
echo ""

# Exécuter la migration SQL
echo "1. Création des tables de notifications..."
mysql -u root -proot perfexcrm < /home/user/Dietzone/modules/dietetic/migrations/add_notifications_system.sql

if [ $? -eq 0 ]; then
    echo "   ✓ Tables créées avec succès"
else
    echo "   ✗ Erreur lors de la création des tables"
    exit 1
fi

echo ""
echo "2. Vérification des tables..."
mysql -u root -proot perfexcrm -e "SHOW TABLES LIKE 'tbldietic_notification%';" | grep -v "Tables_in"

echo ""
echo "3. Configuration du CRON job..."
echo "   Ajoutez cette ligne à votre crontab:"
echo ""
echo "   0 * * * * cd /home/user/Dietzone && php modules/dietetic/cron_notifications.php >> /var/log/dietetic_notifications.log 2>&1"
echo ""
echo "   Pour éditer le crontab: crontab -e"
echo ""

echo "4. Test du système..."
echo "   Pour tester les notifications, exécutez:"
echo ""
echo "   cd /home/user/Dietzone && php modules/dietetic/cron_notifications.php"
echo ""

echo "=========================================="
echo "Installation terminée!"
echo "=========================================="
echo ""
echo "IMPORTANT: Vérifiez aussi:"
echo "  1. Configuration email dans Perfex (Setup > Settings > Email)"
echo "  2. Préférences notifications patient (Portail Patient > Notifications)"
echo ""
