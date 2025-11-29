#!/bin/bash
# Script de suppression du système de facturation custom
# À utiliser pour migrer vers le système Perfex natif

echo "=========================================="
echo "SUPPRESSION DU SYSTÈME DE FACTURATION CUSTOM"
echo "=========================================="
echo ""

# Compteurs
total_files=0
total_dirs=0

# Fonction pour supprimer avec log
delete_file() {
    if [ -f "$1" ]; then
        rm "$1"
        echo "✓ Supprimé: $1"
        ((total_files++))
    fi
}

delete_dir() {
    if [ -d "$1" ]; then
        rm -rf "$1"
        echo "✓ Dossier supprimé: $1"
        ((total_dirs++))
    fi
}

echo "1. SUPPRESSION DES CONTRÔLEURS"
echo "-------------------------------"
delete_file "/home/user/Dietzone/modules/dietetic/controllers/Commissions.php"
delete_file "/home/user/Dietzone/modules/dietetic/controllers/Invoices.php"
delete_file "/home/user/Dietzone/modules/dietetic/controllers/Payment_gateways.php"
delete_file "/home/user/Dietzone/modules/dietetic/controllers/Payments.php"
delete_file "/home/user/Dietzone/modules/dietetic/controllers/Recurring_payments.php"
delete_file "/home/user/Dietzone/modules/dietetic/controllers/Refunds.php"
delete_file "/home/user/Dietzone/modules/dietetic/controllers/Revenue_dashboard.php"
delete_file "/home/user/Dietzone/modules/dietetic/controllers/Service_plans.php"
delete_file "/home/user/Dietzone/modules/dietetic/controllers/Subscriptions.php"

echo ""
echo "2. SUPPRESSION DES MODÈLES"
echo "-------------------------------"
delete_file "/home/user/Dietzone/modules/dietetic/models/Dietetic_commission_settings_model.php"
delete_file "/home/user/Dietzone/modules/dietetic/models/Dietetic_invoices_model.php"
delete_file "/home/user/Dietzone/modules/dietetic/models/Dietetic_payments_model.php"
delete_file "/home/user/Dietzone/modules/dietetic/models/Dietetic_recurring_payments_model.php"
delete_file "/home/user/Dietzone/modules/dietetic/models/Dietetic_refunds_model.php"
delete_file "/home/user/Dietzone/modules/dietetic/models/Dietetic_revenue_shares_model.php"
delete_file "/home/user/Dietzone/modules/dietetic/models/Dietetic_service_plans_model.php"
delete_file "/home/user/Dietzone/modules/dietetic/models/Dietetic_subscriptions_model.php"

echo ""
echo "3. SUPPRESSION DES VUES ADMIN"
echo "-------------------------------"
delete_dir "/home/user/Dietzone/modules/dietetic/views/admin/commissions"
delete_dir "/home/user/Dietzone/modules/dietetic/views/admin/invoices"
delete_dir "/home/user/Dietzone/modules/dietetic/views/admin/payments"
delete_dir "/home/user/Dietzone/modules/dietetic/views/admin/recurring_payments"
delete_dir "/home/user/Dietzone/modules/dietetic/views/admin/refunds"
delete_dir "/home/user/Dietzone/modules/dietetic/views/admin/revenue_dashboard"
delete_dir "/home/user/Dietzone/modules/dietetic/views/admin/service_plans"
delete_dir "/home/user/Dietzone/modules/dietetic/views/admin/subscriptions"

echo ""
echo "4. SUPPRESSION DES VUES AUTRES"
echo "-------------------------------"
delete_dir "/home/user/Dietzone/modules/dietetic/views/payment_gateways"
delete_dir "/home/user/Dietzone/modules/dietetic/views/recurring_payments"
delete_dir "/home/user/Dietzone/modules/dietetic/views/refunds"
delete_file "/home/user/Dietzone/modules/dietetic/views/portal/invoice.php"
delete_file "/home/user/Dietzone/modules/dietetic/views/portal/invoices.php"
delete_file "/home/user/Dietzone/modules/dietetic/views/portal_subscription_view.php"
delete_file "/home/user/Dietzone/modules/dietetic/views/portal_subscriptions.php"

echo ""
echo "5. SUPPRESSION DES FICHIERS DE TEST/DEBUG"
echo "-------------------------------"
delete_file "/home/user/Dietzone/modules/dietetic/test_refunds.php"
delete_file "/home/user/Dietzone/modules/dietetic/debug_views.php"
delete_file "/home/user/Dietzone/modules/dietetic/check_tables.php"
delete_file "/home/user/Dietzone/modules/dietetic/migrations/force_execute_009.php"

echo ""
echo "6. SUPPRESSION DE LA MIGRATION 009"
echo "-------------------------------"
delete_file "/home/user/Dietzone/modules/dietetic/migrations/009_add_recurring_payments_and_refunds.php"

echo ""
echo "=========================================="
echo "RÉSUMÉ"
echo "=========================================="
echo "Fichiers supprimés: $total_files"
echo "Dossiers supprimés: $total_dirs"
echo ""
echo "⚠️  ATTENTION: Les tables de base de données n'ont PAS été supprimées."
echo "Pour supprimer les tables, exécutez le script SQL séparé."
echo ""
