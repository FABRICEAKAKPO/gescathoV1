<?php

namespace App\Http\Controllers;

use App\Models\DemandeMesse;
use App\Models\Celebration;
use App\Models\Recette;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class DemandeMesseController extends Controller
{
    public function index(Request $request): View
    {
        // Construire la requête de base pour les célébrations
        $query = Celebration::with('demandeMesse')
            ->orderBy('date_celebration', 'desc')
            ->orderBy('heure_celebration', 'asc');
        
        // Appliquer les filtres si présents dans la requête
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('demandeMesse', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('demandeur', 'like', '%' . $searchTerm . '%')
                             ->orWhere('type_messe', 'like', '%' . $searchTerm . '%')
                             ->orWhere('numero_recu', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereDate('date_celebration', $searchTerm)
                ->orWhere('heure_celebration', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // Pagination
        $celebrations = $query->paginate(10);
        
        // Statistiques par type de messe
        $statsParType = DemandeMesse::selectRaw('type_messe, COUNT(*) as count')
            ->groupBy('type_messe')
            ->get();
        
        return view('demandes.index', compact('celebrations', 'statsParType'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'demandeur' => 'required|string|max:255',
            'intentions' => 'required|string',
            'type_messe' => 'required|in:QUOTIDIEN,DOMINICAL,TRIDUUM,NEUVAINE,TRENTAINE,MARIAGE,DEFUNT,SPECIALE,VEILLEE,ENTERREMENT',
            'nombre' => 'required|integer|min:1',
            'montant_paye' => 'required|numeric',
            'date_celebration' => 'required|date',
            'heure_celebration' => 'required|date_format:H:i',
        ]);

        // Générer un numéro de reçu unique
        $numeroRecu = 'REC-' . date('Y') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        $demande = DemandeMesse::create([
            'demandeur' => $request->demandeur,
            'intentions' => $request->intentions,
            'type_messe' => $request->type_messe,
            'nombre' => $request->nombre,
            'prix' => $request->montant_paye,
            'montant_paye' => $request->montant_paye,
            'date_celebration' => $request->date_celebration,
            'heure_celebration' => $request->heure_celebration,
            'numero_recu' => $numeroRecu,
            'statut' => 'en_attente',
        ]);

        // Logger la création de la demande de messe
        ActivityLogger::logCreate(DemandeMesse::class, $demande->id, $demande->toArray());

        // Créer automatiquement un enregistrement de recette pour le paiement
        $recette = \App\Models\Recette::create([
            'type' => $request->type_messe,
            'montant' => $request->montant_paye,
            'date' => now()->format('Y-m-d'),
            'demande_messe_id' => $demande->id,
        ]);

        // Logger la création de la recette
        ActivityLogger::logCreate(Recette::class, $recette->id, $recette->toArray());

        // Créer les célébrations en fonction du nombre de fois demandé
        $celebrationsCreated = [];
        if (in_array($request->type_messe, ['TRIDUUM', 'NEUVAINE', 'TRENTAINE'])) {
            // Pour les types de messe multiples
            $nbJoursParCycle = $request->type_messe === 'TRIDUUM' ? 3 : ($request->type_messe === 'NEUVAINE' ? 9 : 30);
            
            $date = \Carbon\Carbon::parse($request->date_celebration);
            
            // Créer chaque cycle du nombre de fois
            for ($cycle = 0; $cycle < $request->nombre; $cycle++) {
                for ($jour = 0; $jour < $nbJoursParCycle; $jour++) {
                    $dateCelebration = $date->copy()->addDays($cycle * $nbJoursParCycle + $jour);
                    
                    $celebration = Celebration::create([
                        'demande_messe_id' => $demande->id,
                        'date_celebration' => $dateCelebration->format('Y-m-d'),
                        'heure_celebration' => $request->heure_celebration,
                        'statut' => 'en_attente',
                    ]);
                    
                    $celebrationsCreated[] = $celebration;
                    // Logger la création de chaque célébration
                    ActivityLogger::logCreate(Celebration::class, $celebration->id, $celebration->toArray());
                }
            }
        } else {
            // Pour les messes simples, créer une célébration par jour pour chaque fois
            $date = \Carbon\Carbon::parse($request->date_celebration);
            for ($i = 0; $i < $request->nombre; $i++) {
                $dateCelebration = $date->copy()->addDays($i);
                
                $celebration = Celebration::create([
                    'demande_messe_id' => $demande->id,
                    'date_celebration' => $dateCelebration->format('Y-m-d'),
                    'heure_celebration' => $request->heure_celebration,
                    'statut' => 'en_attente',
                ]);
                
                $celebrationsCreated[] = $celebration;
                // Logger la création de chaque célébration
                ActivityLogger::logCreate(Celebration::class, $celebration->id, $celebration->toArray());
            }
        }

        return redirect()->route('demandes.index')->with('success', 'Demande de messe enregistrée avec succès');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'demandeur' => 'required|string|max:255',
            'intentions' => 'required|string',
            'type_messe' => 'required|in:QUOTIDIEN,DOMINICAL,TRIDUUM,NEUVAINE,TRENTAINE,MARIAGE,DEFUNT,SPECIALE,VEILLEE,ENTERREMENT',
            'nombre' => 'required|integer|min:1',
            'montant_paye' => 'required|numeric',
            'date_celebration' => 'required|date',
            'heure_celebration' => 'required|date_format:H:i',
        ]);

        // Flasher les données en session pour permettre l'édition (retour à la saisie)
        session()->flashInput($request->all());

        $data = $request->all();

        return view('demandes.preview', compact('data'));
    }

    public function destroy($id): RedirectResponse
    {
        $demande = DemandeMesse::findOrFail($id);
        
        // Logger la suppression de la demande de messe
        ActivityLogger::logDelete(DemandeMesse::class, $demande->id, $demande->toArray());
        
        // Logger la suppression des célébrations associées
        foreach ($demande->celebrations as $celebration) {
            ActivityLogger::logDelete(Celebration::class, $celebration->id, $celebration->toArray());
        }
        
        // Logger la suppression des recettes associées
        foreach ($demande->recettes as $recette) {
            ActivityLogger::logDelete(Recette::class, $recette->id, $recette->toArray());
        }
        
        // Supprimer les célébrations associées
        $demande->celebrations()->delete();
        
        // Supprimer les recettes associées
        $demande->recettes()->delete();
        
        // Supprimer la demande de messe
        $demande->delete();
        
        return redirect()->route('demandes.index')->with('success', 'Demande de messe supprimée avec succès');
    }

    public function recu(int $id): View
    {
        $demande = DemandeMesse::findOrFail($id);
        
        return view('demandes.recu', compact('demande'));
    }
}