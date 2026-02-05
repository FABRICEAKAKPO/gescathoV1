# Guide Utilisateur - Système d'Audit Gescatho

## 🎯 Vue d'ensemble

Le système d'audit de Gescatho enregistre automatiquement toutes les actions effectuées dans le système. En tant qu'administrateur, vous pouvez consulter ces journaux pour vérifier qui a fait quoi et quand.

## 📍 Accéder aux logs d'activité

### Étape 1 : Se connecter
Connectez-vous à Gescatho avec un compte administrateur.

### Étape 2 : Accéder au menu
Dans la barre latérale gauche, vous verrez:
- Tableau de bord
- Demandes
- Recettes
- Dépenses
- Dons
- Rapports
- **Gestion des Utilisateurs** (admin)
- **Journaux d'activité** (admin) ← **CLIQUEZ ICI**

### Étape 3 : Consulter les logs
Vous verrez un tableau listant les 50 derniers logs d'activité.

## 📊 Tableau des logs

### Colonnes

| Colonne | Description |
|---------|-------------|
| **Utilisateur** | Nom de l'utilisateur qui a effectué l'action |
| **Rôle** | Rôle de l'utilisateur (admin, comptable, secrétaire) |
| **Action** | Type d'action (Créé, Modifié, Supprimé) |
| **Entité** | Type d'enregistrement et son ID (ex: Don #123) |
| **Date/Heure** | Quand l'action a été effectuée |
| **Actions** | Lien "Détails" pour voir plus d'informations |

### Badges de couleur

#### Actions
- 🟢 **Créé** (vert) - Nouvel enregistrement créé
- 🟡 **Modifié** (orange) - Enregistrement mis à jour
- 🔴 **Supprimé** (rouge) - Enregistrement supprimé

#### Rôles
- 🔴 **Admin** (rouge) - Administrateur
- 🔵 **Comptable** (bleu) - Comptable
- 🟢 **Secrétaire** (vert) - Secrétaire

## 🔍 Voir les détails d'une action

### Procédure

1. Cliquez sur le lien **"Détails"** d'une ligne du tableau
2. Vous verrez une page détaillée avec:

### Section "Utilisateur"
- **Nom** : Nom complet de l'utilisateur
- **Rôle** : admin, comptable, ou secrétaire
- **Adresse IP** : Adresse IP depuis laquelle l'action a été effectuée

### Section "Action"
- **Type d'action** : Créé, Modifié, Supprimé
- **Entité** : Type et ID de l'enregistrement (ex: Don #42)
- **Date/Heure** : Timestamp exact de l'action

### Section "Valeurs" (pour les modifications)

#### Modifications (Modifié)
Deux colonnes côte à côte:
- **Valeurs avant** (rouge)
- **Valeurs après** (vert)

Comparaison visuelle des changements.

#### Créations (Créé)
Liste verte des données créées.

#### Suppressions (Supprimé)
Liste rouge des données supprimées.

## 📋 Types d'enregistrements tracés

Le système d'audit enregistre les actions sur:

| Type | Créé | Modifié | Supprimé |
|------|------|---------|----------|
| **Don** | ✅ | ✅ | ✅ |
| **Dépense de Don** | ✅ | ✅ | ✅ |
| **Recette** | ✅ | ❌ | ✅ |
| **Dépense** | ✅ | ❌ | ✅ |

## 🔄 Pagination

- Par défaut, 50 logs sont affichés par page
- Utilisez les boutons en bas pour naviguer:
  - **Previous** : Aller à la page précédente
  - Numéros de page : Aller à une page spécifique
  - **Next** : Aller à la page suivante

## 💡 Cas d'usage courants

### 1. Vérifier qui a créé un don
1. Allez à "Journaux d'activité"
2. Cherchez une ligne avec:
   - Action: "Créé" (badge vert)
   - Entité: "Don #123"
3. Cliquez "Détails"

### 2. Vérifier les modifications d'une recette
1. Allez à "Journaux d'activité"
2. Cherchez une ligne avec:
   - Action: "Modifié" (badge orange)
   - Entité: "Recette #456"
3. Cliquez "Détails" pour voir quoi a changé

### 3. Auditer les actions d'un utilisateur
1. Allez à "Journaux d'activité"
2. Regardez la colonne "Utilisateur" pour trouver le nom
3. Cliquez sur les lignes pour voir chaque action

### 4. Vérifier une suppression douteuse
1. Allez à "Journaux d'activité"
2. Cherchez une ligne avec:
   - Action: "Supprimé" (badge rouge)
3. Cliquez "Détails" pour voir ce qui a été supprimé

## 🔒 Sécurité et permissions

- **Seuls les administrateurs** peuvent voir les logs d'activité
- Les logs sont créés **automatiquement** pour toutes les actions
- L'**adresse IP** est enregistrée pour chaque action
- Les noms d'utilisateurs sont **snapshots** au moment de l'action
- Les logs ne peuvent **pas être modifiés** (audit trail immuable)

## ⏰ Informations temporelles

- Chaque log est horodaté avec la date ET l'heure exacte
- Format: `JJ/MM/AAAA HH:MM:SS`
- Exemple: `27/01/2025 14:30:45`

## 📲 Données sauvegardées

Pour chaque action, le système enregistre:

```
✅ Qui a effectué l'action (utilisateur, rôle)
✅ Quand (date et heure exactes)
✅ D'où (adresse IP)
✅ Quoi (type d'entité, ID)
✅ Comment (create/update/delete)
✅ Avant/Après (valeurs pour modifications)
```

## ⚠️ Limitations

- Les logs ne peuvent **pas être supprimés** par les utilisateurs
- Les logs ne peuvent **pas être modifiés**
- Seuls les logs des **4 dernières semaines** sont affichés par défaut
- Les administrateurs ne voient **pas les détails des mots de passe**

## 🤔 Questions fréquentes

### Q: Qui peut voir les logs d'activité?
**R:** Uniquement les administrateurs.

### Q: Combien de temps les logs sont-ils conservés?
**R:** Indéfiniment (sauf si une politique d'archivage est mise en place).

### Q: Est-ce que les actions des administrateurs sont tracées?
**R:** Oui, toutes les actions, y compris celles des administrateurs.

### Q: Les logs incluent-ils les adresses IP?
**R:** Oui, l'adresse IP source est enregistrée.

### Q: Je peux restaurer un enregistrement supprimé?
**R:** Les logs affichent les données supprimées, mais la restauration manuelle est nécessaire.

## 📞 Besoin d'aide?

Pour plus d'informations techniques, consultez:
- `AUDIT_SYSTEM.md` - Documentation technique complète
- `AUDIT_IMPLEMENTATION.md` - Détails d'implémentation
- `AUDIT_SCHEMA.json` - Schéma de la base de données

## 🚀 Astuces

1. **Tri chronologique** : Les logs les plus récents apparaissent en premier
2. **Recherche par action** : Cherchez "Supprimé" pour voir les suppressions
3. **Recherche par entité** : Regardez l'ID pour localiser une entité spécifique
4. **Vérification utilisateur** : Cliquez sur plusieurs logs d'un même utilisateur pour voir son historique complet
5. **Comparaison avant/après** : Utilisez les sections colorées (rouge/vert) pour identifier rapidement les changements

---

**Dernière mise à jour**: 27 janvier 2025
**Version du système**: 1.0
