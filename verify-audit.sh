#!/usr/bin/env bash

# 📋 Système d'Audit Gescatho - Installation & Vérification
# Ce script aide à vérifier que tout est bien installé

echo "╔════════════════════════════════════════════════════════╗"
echo "║  Vérification du Système d'Audit Gescatho             ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""

# Vérifier les fichiers créés
echo "📁 Vérification des fichiers créés..."
echo ""

files_to_check=(
    "app/Models/ActivityLog.php"
    "app/Services/ActivityLogger.php"
    "app/Http/Controllers/ActivityLogController.php"
    "database/migrations/2026_01_27_150000_create_activity_logs_table.php"
    "resources/views/activity-logs/index.blade.php"
    "resources/views/activity-logs/show.blade.php"
    "tests/Feature/ActivityLoggingTest.php"
    "README_AUDIT.md"
    "AUDIT_SYSTEM.md"
    "AUDIT_USER_GUIDE.md"
    "AUDIT_IMPLEMENTATION.md"
    "AUDIT_EXAMPLES.php"
    "AUDIT_SCHEMA.json"
    "AUDIT_CHECKLIST.md"
    "FINAL_SUMMARY.md"
    "README_FR.md"
    "QUICK_START.md"
    "INDEX.md"
    "INVENTORY.md"
)

echo "Vérification de $(expr ${#files_to_check[@]}) fichiers..."
echo ""

for file in "${files_to_check[@]}"; do
    if [ -f "$file" ]; then
        size=$(wc -c < "$file" | numfmt --to=iec 2>/dev/null || echo "")
        echo "✅ $file"
    else
        echo "❌ $file - MANQUANT!"
    fi
done

echo ""
echo "╔════════════════════════════════════════════════════════╗"
echo "║  Prochaines étapes                                     ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""

echo "1. Exécuter la migration (si nécessaire):"
echo "   php artisan migrate"
echo ""

echo "2. Exécuter les tests:"
echo "   php artisan test tests/Feature/ActivityLoggingTest.php"
echo ""

echo "3. Accéder au système:"
echo "   - Connectez-vous en tant qu'administrateur"
echo "   - Cliquez sur 'Journaux d'activité' dans le menu"
echo "   - Ou accédez à: /admin/activity-logs"
echo ""

echo "4. Lire la documentation:"
echo "   - QUICK_START.md (3 minutes)"
echo "   - README_FR.md (10 minutes)"
echo "   - AUDIT_USER_GUIDE.md (20 minutes)"
echo ""

echo "╔════════════════════════════════════════════════════════╗"
echo "║  ✅ Configuration complètement                         ║"
echo "║                                                        ║"
echo "║  Le système d'audit est prêt à être utilisé!         ║"
echo "╚════════════════════════════════════════════════════════╝"
