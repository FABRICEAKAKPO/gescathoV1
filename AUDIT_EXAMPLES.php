<?php

// 📚 EXEMPLES D'UTILISATION - Service ActivityLogger
// Ce fichier montre comment utiliser le service d'audit dans vos contrôleurs

namespace App\Http\Controllers;

use App\Models\Don;
use App\Models\Recette;
use App\Models\Depense;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ExampleAuditController extends Controller
{
    /**
     * ===== EXEMPLE 1: ENREGISTREMENT DE CRÉATION =====
     * 
     * Quand utiliser:
     * - Dans la méthode store() d'un contrôleur
     * - Après la création d'un nouvel enregistrement
     * - Pour tracer la création initiale
     */
    public function exampleCreate(Request $request): RedirectResponse
    {
        // Valider les données
        $validated = $request->validate([
            'donateur' => 'nullable|string|max:255',
            'type_don' => 'required|in:DON,DIME,COLLECTE',
            'montant' => 'required|numeric|min:0',
            'date_don' => 'required|date',
        ]);

        // Créer l'enregistrement
        $don = Don::create($validated);

        // 🔑 ENREGISTRER LA CRÉATION
        // Syntax: ActivityLogger::logCreate(ModelClass, modelId, newValues)
        ActivityLogger::logCreate(
            Don::class,                    // Classe du modèle
            $don->id,                      // ID de l'enregistrement créé
            $don->toArray()                // Toutes les valeurs créées
        );

        return redirect()->route('dons.index')
            ->with('success', 'Don créé avec succès et enregistré dans l\'audit');
    }

    /**
     * ===== EXEMPLE 2: ENREGISTREMENT DE MODIFICATION =====
     * 
     * Quand utiliser:
     * - Dans la méthode update() d'un contrôleur
     * - Après la modification d'un enregistrement existant
     * - Pour tracer les changements avant/après
     * 
     * ⚠️ IMPORTANT: Capture les valeurs AVANT et APRÈS la modification
     */
    public function exampleUpdate(Request $request, Don $don): RedirectResponse
    {
        // Valider les données
        $validated = $request->validate([
            'donateur' => 'nullable|string|max:255',
            'type_don' => 'required|in:DON,DIME,COLLECTE',
            'montant' => 'required|numeric|min:0',
            'date_don' => 'required|date',
        ]);

        // 🔑 CAPTURE LES VALEURS AVANT MODIFICATION
        // Ceci est OBLIGATOIRE pour enregistrer les changements
        $oldValues = $don->toArray();

        // Mettre à jour l'enregistrement
        $don->update($validated);

        // 🔑 ENREGISTRER LA MODIFICATION
        // Syntax: ActivityLogger::logUpdate(ModelClass, modelId, oldValues, newValues)
        ActivityLogger::logUpdate(
            Don::class,                    // Classe du modèle
            $don->id,                      // ID de l'enregistrement
            $oldValues,                    // Valeurs avant modification
            $don->refresh()->toArray()     // Valeurs après modification
        );

        return redirect()->route('dons.index')
            ->with('success', 'Don modifié avec succès et changements enregistrés');
    }

    /**
     * ===== EXEMPLE 3: ENREGISTREMENT DE SUPPRESSION =====
     * 
     * Quand utiliser:
     * - Dans la méthode destroy() d'un contrôleur
     * - Avant la suppression d'un enregistrement
     * - Pour tracer ce qui a été supprimé
     * 
     * ⚠️ IMPORTANT: Enregistrer AVANT la suppression pour capturer les données
     */
    public function exampleDestroy(Don $don): RedirectResponse
    {
        // 🔑 CAPTURE LES DONNÉES AVANT SUPPRESSION
        // Ceci permet de voir ce qui a été supprimé dans les logs
        $donData = $don->toArray();

        // 🔑 ENREGISTRER LA SUPPRESSION
        // Syntax: ActivityLogger::logDelete(ModelClass, modelId, deletedValues)
        ActivityLogger::logDelete(
            Don::class,        // Classe du modèle
            $don->id,          // ID de l'enregistrement supprimé
            $donData           // Snapshot complet avant suppression
        );

        // Maintenant supprimer l'enregistrement
        $don->delete();

        return redirect()->route('dons.index')
            ->with('success', 'Don supprimé avec succès et enregistré dans l\'audit');
    }

    /**
     * ===== EXEMPLE 4: ENREGISTREMENT PERSONNALISÉ =====
     * 
     * Quand utiliser:
     * - Pour des actions personnalisées
     * - Quand les méthodes standard ne suffisent pas
     * - Pour un contrôle complet
     */
    public function exampleCustomLog(Request $request, Don $don): RedirectResponse
    {
        // Effectuer une action personnalisée
        $oldValues = $don->toArray();
        $don->statut = 'VALIDÉ';
        $don->validated_by = auth()->id();
        $don->validated_at = now();
        $don->save();

        // 🔑 ENREGISTREMENT PERSONNALISÉ
        // Syntax: ActivityLogger::log(action, modelClass, modelId, oldValues, newValues)
        ActivityLogger::log(
            'validate',                    // Action personnalisée
            Don::class,                    // Classe du modèle
            $don->id,                      // ID de l'enregistrement
            $oldValues,                    // Valeurs avant
            $don->refresh()->toArray()     // Valeurs après
        );

        return redirect()->route('dons.index')
            ->with('success', 'Don validé et action enregistrée');
    }

    /**
     * ===== EXEMPLE 5: PLUSIEURS ACTIONS TRACÉES =====
     * 
     * Cas réel: Créer un don ET ses dépenses associées
     */
    public function exampleMultipleActions(Request $request): RedirectResponse
    {
        // Action 1: Créer un don
        $don = Don::create([
            'donateur' => $request->donateur,
            'type_don' => $request->type_don,
            'montant' => $request->montant,
            'date_don' => $request->date_don,
        ]);

        // Enregistrer la création du don
        ActivityLogger::logCreate(Don::class, $don->id, $don->toArray());

        // Action 2: Créer une dépense associée
        $depense = $don->depenses()->create([
            'motif' => $request->motif_depense,
            'montant' => $request->montant_depense,
            'date_depense' => $request->date_depense,
        ]);

        // Enregistrer la création de la dépense
        ActivityLogger::logCreate(
            $depense::class,  // Classe de la dépense
            $depense->id,
            $depense->toArray()
        );

        return redirect()->route('dons.index')
            ->with('success', 'Don et dépense créés et enregistrés dans l\'audit');
    }

    /**
     * ===== EXEMPLE 6: GESTION D'ERREUR AVEC AUDIT =====
     * 
     * Assurer que l'audit est enregistré même en cas d'erreur
     */
    public function exampleErrorHandling(Request $request, Don $don)
    {
        try {
            $oldValues = $don->toArray();

            // Faire quelque chose
            if (!$this->validateDonWithExternalService($don)) {
                throw new \Exception('Validation externe échouée');
            }

            $don->update(['statut' => 'APPROUVÉ']);

            // Enregistrer le succès
            ActivityLogger::logUpdate(
                Don::class,
                $don->id,
                $oldValues,
                $don->refresh()->toArray()
            );

            return redirect()->back()->with('success', 'Don approuvé');

        } catch (\Exception $e) {
            // Enregistrer l'erreur si nécessaire
            \Log::error('Erreur lors de l\'approbation du don', [
                'don_id' => $don->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    // Méthode utilitaire
    private function validateDonWithExternalService(Don $don): bool
    {
        return true;
    }
}

/**
 * ===== RÉCAPITULATIF DES PATTERNS =====
 * 
 * PATTERN 1: Création
 * ───────────────────
 * $model = Model::create($data);
 * ActivityLogger::logCreate(Model::class, $model->id, $model->toArray());
 * 
 * 
 * PATTERN 2: Modification
 * ──────────────────────
 * $oldValues = $model->toArray();
 * $model->update($data);
 * ActivityLogger::logUpdate(Model::class, $model->id, $oldValues, $model->refresh()->toArray());
 * 
 * 
 * PATTERN 3: Suppression
 * ─────────────────────
 * ActivityLogger::logDelete(Model::class, $model->id, $model->toArray());
 * $model->delete();
 * 
 * 
 * ===== POINTS IMPORTANTS =====
 * 
 * 1. ORDRE: Enregistrer AVANT de supprimer
 * 2. OLD VALUES: Capturer avant de modifier
 * 3. NEW VALUES: Capturer après la modification (ou utiliser refresh())
 * 4. CLASS: Utiliser Model::class plutôt que des strings
 * 5. ARRAY: Utiliser toArray() pour la sérialisation JSON
 * 
 * 
 * ===== DONNÉES AUTOMATIQUEMENT ENREGISTRÉES =====
 * 
 * Le service enregistre automatiquement:
 * • user_id - ID de l'utilisateur connecté
 * • user_name - Nom snapshot
 * • user_role - Rôle snapshot
 * • ip_address - IP source
 * • user_agent - Navigateur
 * • created_at - Timestamp
 * 
 * Vous ne devez fournir que:
 * • action - create, update, delete
 * • model - Classe du modèle
 * • modelId - ID de l'enregistrement
 * • oldValues - (pour update/delete)
 * • newValues - (pour create/update)
 */

?>

<!-- 
  ===== UTILISATION DANS LES VUES (EXEMPLE OPTIONNEL) =====
  
  Afficher les logs d'une entité dans la vue:
  
  @if($model->activityLogs->count() > 0)
      <div class="activity-history">
          <h3>Historique</h3>
          @foreach($model->activityLogs as $log)
              <p>{{ $log->user_name }} a {{ $log->getActionLabel() }} le {{ $log->created_at->format('d/m/Y H:i') }}</p>
          @endforeach
      </div>
  @endif
  
  Note: Cela nécessite une relation activityLogs sur le modèle:
  
  public function activityLogs()
  {
      return $this->morphMany(ActivityLog::class, 'model');
  }
-->
