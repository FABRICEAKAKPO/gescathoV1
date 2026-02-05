# Système d'Audit - Gescatho

## Vue d'ensemble

Le système d'audit de Gescatho permet de tracer toutes les actions effectuées par les utilisateurs du système. Chaque création, modification ou suppression d'enregistrement est enregistrée dans la base de données avec les détails suivants :

- **Utilisateur** : Nom, rôle, et adresse IP
- **Action** : Type d'action (create, update, delete)
- **Entité** : Type de modèle et ID de l'enregistrement
- **Valeurs** : Valeurs avant et après modification (pour les updates)
- **Timestamp** : Date et heure exacte de l'action

## Entités auditées

Le système d'audit enregistre actuellement les actions sur :

- **Dons** (Don)
- **Dépenses de Dons** (DepenseDon)
- **Recettes** (Recette)
- **Dépenses** (Depense)

## Architecture

### Table `activity_logs`

La table stocke les journaux d'activité avec la structure suivante :

```sql
- id
- user_id (FK vers users)
- user_name (snapshot du nom)
- user_role (snapshot du rôle)
- action (string: create, update, delete)
- model (string: Don, Recette, etc.)
- model_id (bigint: ID de l'entité)
- old_values (JSON: valeurs avant modification)
- new_values (JSON: valeurs après création/modification)
- ip_address (string: adresse IP du client)
- user_agent (string: navigateur/client)
- created_at, updated_at
```

### Service `ActivityLogger`

Le service `App\Services\ActivityLogger` fournit des méthodes statiques pour enregistrer les actions :

```php
// Enregistrer une création
ActivityLogger::logCreate(Model::class, $id, $newValues);

// Enregistrer une modification
ActivityLogger::logUpdate(Model::class, $id, $oldValues, $newValues);

// Enregistrer une suppression
ActivityLogger::logDelete(Model::class, $id, $oldValues);
```

### Modèle `ActivityLog`

Le modèle Eloquent `App\Models\ActivityLog` fournit :

- Relation `user()` : Lien vers l'utilisateur
- Méthode `getActionLabel()` : Retourne le label en français (Créé, Modifié, Supprimé)
- Méthode `getActionBadgeColor()` : Retourne la couleur du badge selon l'action
- Cast JSON pour `old_values` et `new_values`

## Interface d'administration

### Routes

- `GET /admin/activity-logs` : Liste paginée de tous les journaux
- `GET /admin/activity-logs/{id}` : Détails d'un journal spécifique

### Fonctionnalités

#### Page de liste (`activity-logs.index`)

- Tableau paginé des 50 derniers logs
- Colonnes : Utilisateur, Rôle, Action, Entité, Date/Heure
- Badges colorés pour les rôles et actions
- Lien "Détails" pour voir les changements spécifiques

#### Page de détails (`activity-logs.show`)

- Informations complètes de l'utilisateur
- Détails de l'action
- Pour les modifications : Comparaison avant/après
- Pour les créations : Affichage des données créées
- Pour les suppressions : Affichage des données supprimées

## Intégration dans les contrôleurs

### Exemple d'intégration complète

```php
// Dans store() - Création
$model = Model::create([...]);
ActivityLogger::logCreate(Model::class, $model->id, $model->toArray());

// Dans update() - Modification
$oldValues = $model->toArray();
$model->update([...]);
ActivityLogger::logUpdate(Model::class, $model->id, $oldValues, $model->refresh()->toArray());

// Dans destroy() - Suppression
ActivityLogger::logDelete(Model::class, $model->id, $model->toArray());
$model->delete();
```

## Contrôles d'accès

- **Accès à la page des logs** : Réservé aux administrateurs
- **Enregistrement automatique** : Tous les utilisateurs (admin, comptable, secrétaire)
- **Informations tracées** : Nom d'utilisateur, rôle, adresse IP

## Données sauvegardées

Le système sauvegarde les snapshots des données :

- **old_values** : État avant la modification (pour update et delete)
- **new_values** : État après la modification (pour create et update)
- **user_name** : Snapshot du nom d'utilisateur au moment de l'action
- **user_role** : Snapshot du rôle au moment de l'action
- **ip_address** : Adresse IP source de la requête
- **user_agent** : Informations du navigateur/client

## Cas d'utilisation

1. **Traçabilité** : Vérifier qui a créé/modifié/supprimé un enregistrement
2. **Audit de conformité** : Démontrer les contrôles et la traçabilité
3. **Récupération d'informations** : Voir l'historique complet des modifications
4. **Détection d'anomalies** : Identifier les actions inhabituelles
5. **Réclamations** : Résoudre les disputes sur les modifications

## Migration

La migration `2026_01_27_150000_create_activity_logs_table` a créé la table avec :
- Index sur (user_id, created_at) pour les requêtes par utilisateur et date
- Index sur (model, action) pour les requêtes par type d'entité et action
- Contrainte de clé étrangère sur user_id avec suppression en cascade

## Considérations de performance

- Les logs sont insérés de manière synchrone (bloquant)
- Les indexes permettent des requêtes rapides
- La pagination par 50 évite de charger trop de données à la fois
- Les valeurs JSON permettent de stocker des données flexibles

## Améliorations futures

- [ ] Filtrage avancé par date, utilisateur, action, entité
- [ ] Export des logs (CSV, PDF)
- [ ] Recherche en texte libre
- [ ] Alertes sur certaines actions (suppressions en masse)
- [ ] Archive des anciens logs (> 1 an)
- [ ] Intégration avec un système de logging externe (ELK, Splunk)
