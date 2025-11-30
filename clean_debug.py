#!/usr/bin/env python3
"""
Script pour nettoyer les logs de debug dans le module Dietetic
"""
import re
import os
import glob

# Patterns de remplacement
PATTERNS = [
    # Logs avec emojis et [DEBUG]
    (r"log_activity\('🔍 \[MODEL DEBUG\](.*?)'\)", r"dietetic_debug_log('MODEL:\1', 'debug')"),
    (r'log_activity\("🔍 \[MODEL DEBUG\](.*?)"\)', r'dietetic_debug_log("MODEL:\1", "debug")'),

    (r"log_activity\('✅ \[MODEL DEBUG\](.*?)'\)", r"dietetic_debug_log('MODEL:\1', 'success')"),
    (r'log_activity\("✅ \[MODEL DEBUG\](.*?)"\)', r'dietetic_debug_log("MODEL:\1", "success")'),

    (r"log_activity\('⚠️ \[MODEL DEBUG\](.*?)'\)", r"dietetic_debug_log('MODEL:\1', 'warning')"),
    (r'log_activity\("⚠️ \[MODEL DEBUG\](.*?)"\)', r'dietetic_debug_log("MODEL:\1", "warning")'),

    (r"log_activity\('❌ \[MODEL DEBUG\](.*?)'\)", r"dietetic_debug_log('MODEL:\1', 'error')"),
    (r'log_activity\("❌ \[MODEL DEBUG\](.*?)"\)', r'dietetic_debug_log("MODEL:\1", "error")'),

    # Logs contrôleurs
    (r"log_activity\('🔍 \[DEBUG\](.*?)'\)", r"dietetic_debug_log('\1', 'debug')"),
    (r'log_activity\("🔍 \[DEBUG\](.*?)"\)', r'dietetic_debug_log("\1", "debug")'),

    (r"log_activity\('\[TEST_PUSH DEBUG\](.*?)'\)", r"dietetic_debug_log('TEST_PUSH:\1', 'debug')"),
    (r'log_activity\("\[TEST_PUSH DEBUG\](.*?)"\)', r'dietetic_debug_log("TEST_PUSH:\1", "debug")'),

    (r"log_activity\('\[DIETETIC DEBUG\](.*?)'\)", r"dietetic_debug_log('\1', 'debug')"),
    (r'log_activity\("\[DIETETIC DEBUG\](.*?)"\)', r'dietetic_debug_log("\1", "debug")'),

    # Logs DEBUG: simple
    (r"log_activity\('DEBUG:(.*?)'\)", r"dietetic_debug_log('\1', 'debug')"),
    (r'log_activity\("DEBUG:(.*?)"\)', r'dietetic_debug_log("\1", "debug")'),
]

def clean_file(file_path):
    """Nettoie un fichier PHP des logs debug"""
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content
    replacements_count = 0

    for pattern, replacement in PATTERNS:
        content, count = re.subn(pattern, replacement, content)
        replacements_count += count

    if replacements_count > 0:
        # Backup
        backup_path = file_path + '.backup'
        with open(backup_path, 'w', encoding='utf-8') as f:
            f.write(original_content)

        # Save modified
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)

        return replacements_count

    return 0

def main():
    base_dir = '/home/user/Dietzone/modules/dietetic'

    files_to_clean = [
        f'{base_dir}/models/Dietetic_notifications_model.php',
        f'{base_dir}/controllers/Notifications.php',
        f'{base_dir}/controllers/Portal.php',
        f'{base_dir}/controllers/Food_surveys.php',
    ]

    total_replacements = 0
    files_modified = 0

    print("🧹 Nettoyage des logs de debug...\n")

    for file_path in files_to_clean:
        if not os.path.exists(file_path):
            print(f"⚠️  Fichier non trouvé: {file_path}")
            continue

        count = clean_file(file_path)
        if count > 0:
            files_modified += 1
            total_replacements += count
            print(f"✅ {os.path.basename(file_path)}: {count} remplacement(s)")
        else:
            print(f"ℹ️  {os.path.basename(file_path)}: Aucun remplacement")

    print(f"\n🎉 Terminé!")
    print(f"   {files_modified} fichier(s) modifié(s)")
    print(f"   {total_replacements} remplacement(s) total")

if __name__ == '__main__':
    main()
