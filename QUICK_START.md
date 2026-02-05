# ⚡ DÉMARRAGE RAPIDE - Système d'Audit Gescatho

## 🎯 3 Minutes pour commencer

### Étape 1: Accéder au système (30 secondes)
1. Connectez-vous à Gescatho en tant qu'**administrateur**
2. Regardez le menu latéral (gauche)
3. Cliquez sur **"Journaux d'activité"**

### Étape 2: Voir les logs (1 minute)
Vous voyez un tableau avec:
- **Utilisateur** - Qui a fait l'action
- **Rôle** - admin/comptable/secrétaire
- **Action** - Créé / Modifié / Supprimé
- **Entité** - Don #42, Recette #5, etc.
- **Date/Heure** - Quand
- **Détails** - Bouton pour voir plus

### Étape 3: Voir les détails (1,5 minutes)
1. Cliquez sur **"Détails"** d'une ligne
2. Vous voyez:
   - **Informations utilisateur** (IP, navigateur)
   - **Type d'action** avec badge coloré
   - **Valeurs avant/après** (pour modifications)

## 🎨 Couleurs des badges

### Actions
- 🟢 **Créé** (vert) = nouvel enregistrement
- 🟡 **Modifié** (orange) = mise à jour
- 🔴 **Supprimé** (rouge) = suppression

### Rôles
- 🔴 **Admin** (rouge)
- 🔵 **Comptable** (bleu)
- 🟢 **Secrétaire** (vert)

## 📍 URLs directes

```
/admin/activity-logs              → Liste des logs
/admin/activity-logs/42           → Détails du log 42
```

## 📚 Documentation rapide

| Besoin | Fichier |
|--------|---------|
| Comprendre rapidement | README_FR.md |
| Apprendre à utiliser | AUDIT_USER_GUIDE.md |
| Voir l'architecture | AUDIT_SYSTEM.md |
| Exemples de code | AUDIT_EXAMPLES.php |
| Trouver des infos | INDEX.md |

## 🎯 Cas courants

### "Je veux voir qui a créé ce don"
1. Allez à `/admin/activity-logs`
2. Cherchez une ligne avec "Créé" et "Don #42"
3. Cliquez "Détails"

### "Je veux voir ce qui a changé"
1. Allez à `/admin/activity-logs`
2. Cherchez une ligne avec "Modifié"
3. Cliquez "Détails"
4. Comparez **Avant** (rouge) et **Après** (vert)

### "Je veux auditer un utilisateur"
1. Allez à `/admin/activity-logs`
2. Regardez la colonne "Utilisateur"
3. Cliquez sur chaque action de cet utilisateur

## ⚙️ Pour les développeurs

### Ajouter du logging à un contrôleur

```php
use App\Services\ActivityLogger;

// Lors d'une création
$model = Model::create($data);
ActivityLogger::logCreate(Model::class, $model->id, $model->toArray());

// Lors d'une modification
$oldValues = $model->toArray();
$model->update($data);
ActivityLogger::logUpdate(Model::class, $model->id, $oldValues, $model->refresh()->toArray());

// Lors d'une suppression
ActivityLogger::logDelete(Model::class, $model->id, $model->toArray());
$model->delete();
```

## ✅ Vérification rapide

Taper dans le terminal:

```bash
# Voir le nombre de logs
php artisan tinker
> DB::table('activity_logs')->count()

# Voir la dernière action
> DB::table('activity_logs')->latest()->first()

# Quitter tinker
> exit
```

## 🔒 Sécurité

- ✅ Seul les **administrateurs** voient les logs
- ✅ Les **logs ne peuvent pas être modifiés**
- ✅ **IP et User Agent** sont enregistrés
- ✅ **Protection CSRF** active

## 📊 Données enregistrées

Pour chaque action:
```json
{
  "user_id": 1,
  "user_name": "Admin",
  "user_role": "admin",
  "action": "create|update|delete",
  "model": "App\\Models\\Don",
  "model_id": 42,
  "old_values": {...},
  "new_values": {...},
  "ip_address": "192.168.1.1",
  "user_agent": "Mozilla/5.0...",
  "created_at": "2025-01-27 14:30:45"
}
```

## 🚀 Prochaines étapes

1. ✅ Allez à `/admin/activity-logs`
2. ✅ Explorez les logs
3. ✅ Lisez **AUDIT_USER_GUIDE.md** pour plus
4. ✅ Lisez **AUDIT_SYSTEM.md** si développeur

## 💡 Conseils

- **Tri** : Les logs les plus récents en haut
- **Recherche** : Utilisez le nom d'utilisateur
- **Pagination** : Cliquez "Next" pour plus de logs
- **Détails** : Cliquez "Détails" pour voir les changements

## 🆘 Problèmes?

### "Je ne vois pas le menu 'Journaux d'activité'"
→ Vérifiez que vous êtes **administrateur**

### "Je ne vois pas de logs"
→ Créez/modifiez un enregistrement (don, recette, etc.)

### "Je veux ajouter du logging"
→ Consultez **AUDIT_EXAMPLES.php**

## 📞 Documentation complète

- **README_FR.md** - Pour francophones
- **INDEX.md** - Navigation
- **AUDIT_USER_GUIDE.md** - Guide complet
- **AUDIT_SYSTEM.md** - Architecture
- **AUDIT_EXAMPLES.php** - Exemples

## ✨ Prêt?

Cliquez sur **"Journaux d'activité"** dans le menu et explorez! 🎉

---

**Temps de lecture**: 3 minutes
**Temps d'utilisation**: < 1 minute
**Complexité**: Simple ⭐
