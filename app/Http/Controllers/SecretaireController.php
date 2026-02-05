<?php

namespace App\Http\Controllers;

use App\Models\DemandeMesse;
use App\Models\Recette;
use App\Models\Depense;
use App\Models\Celebration;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class SecretaireController extends Controller
{
    public function index(): View
    {
        // Calculer les statistiques pour le secrétaire
        $stats = [
            'demandes_jour' => DemandeMesse::whereDate('created_at', Carbon::today())->count(),
            'demandes_total' => DemandeMesse::count(),
        ];

        // Récupérer les demandes récentes
        $recentes_demandes = DemandeMesse::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('secretaire.dashboard', compact('stats', 'recentes_demandes'));
    }

    public function demandes(Request $request): View
    {
        $query = DemandeMesse::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('demandeur', 'like', "%$search%")
                  ->orWhere('type_messe', 'like', "%$search%")
                  ->orWhere('date_celebration', 'like', "%$search%");
        }

        $demandes = $query->with(['celebrations' => function($query) {
            $query->orderBy('date_celebration', 'asc')
                  ->orderBy('heure_celebration', 'asc');
        }])
        ->orderBy('date_celebration', 'desc')
        ->orderBy('heure_celebration', 'asc')
        ->paginate(20);
        
        // Statistiques par type de messe
        $statsParType = DemandeMesse::selectRaw('type_messe, COUNT(*) as count')
            ->groupBy('type_messe')
            ->get();
        
        return view('secretaire.demandes', compact('demandes', 'statsParType'));
    }

    public function storeDemande(Request $request): RedirectResponse
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

        $numeroRecu = 'REC-' . date('Y') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        $demande = DemandeMesse::create([
            'demandeur' => $request->demandeur,
            'intentions' => $request->intentions,
            'type_messe' => $request->type_messe,
            'nombre' => $request->nombre ?? 1,
            'prix' => $request->montant_paye, // Pour simplifier, on utilise le montant payé comme prix
            'montant_paye' => $request->montant_paye,
            'date_celebration' => $request->date_celebration,
            'heure_celebration' => $request->heure_celebration,
            'numero_recu' => $numeroRecu,
            'statut' => 'en_attente',
        ]);

        // Créer automatiquement un enregistrement de recette pour le paiement
        \App\Models\Recette::create([
            'type' => $request->type_messe,
            'montant' => $request->montant_paye,
            'date' => now()->format('Y-m-d'),
            'demande_messe_id' => $demande->id,
        ]);

        // Créer les célébrations en fonction du nombre de fois demandé
        if (in_array($request->type_messe, ['TRIDUUM', 'NEUVAINE', 'TRENTAINE'])) {
            $nbJoursParCycle = $request->type_messe === 'TRIDUUM' ? 3 : ($request->type_messe === 'NEUVAINE' ? 9 : 30);
            $date = \Carbon\Carbon::parse($request->date_celebration);

            for ($cycle = 0; $cycle < $request->nombre; $cycle++) {
                for ($jour = 0; $jour < $nbJoursParCycle; $jour++) {
                    $dateCelebration = $date->copy()->addDays($cycle * $nbJoursParCycle + $jour);

                    \App\Models\Celebration::create([
                        'demande_messe_id' => $demande->id,
                        'date_celebration' => $dateCelebration->format('Y-m-d'),
                        'heure_celebration' => $request->heure_celebration,
                    ]);
                }
            }
        } else {
            // Pour les messes simples, créer une célébration par jour pour chaque fois
            $date = \Carbon\Carbon::parse($request->date_celebration);
            for ($i = 0; $i < $request->nombre; $i++) {
                $dateCelebration = $date->copy()->addDays($i);

                \App\Models\Celebration::create([
                    'demande_messe_id' => $demande->id,
                    'date_celebration' => $dateCelebration->format('Y-m-d'),
                    'heure_celebration' => $request->heure_celebration,
                ]);
            }
        }

        return redirect()->route('secretaire.demandes')->with('success', 'Demande de messe enregistrée avec succès');
    }

    public function recettes(): View
    {
        $recettes = Recette::with('demandeMesse')
            ->orderBy('date', 'desc')
            ->paginate(20);
        
        $total_recettes = Recette::sum('montant');
        
        return view('secretaire.recettes', compact('recettes', 'total_recettes'));
    }

    public function storeRecette(Request $request): RedirectResponse
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'montant' => 'required|numeric',
            'date' => 'required|date',
        ]);

        Recette::create([
            'type' => $request->type,
            'montant' => $request->montant,
            'date' => $request->date,
        ]);

        return redirect()->route('secretaire.recettes')->with('success', 'Recette enregistrée avec succès');
    }

    public function depenses(): View
    {
        // Pour le secrétaire, on peut limiter aux dépenses liées à son compte
        $depenses = Depense::orderBy('date', 'desc')
            ->paginate(20);
        
        $total_depenses = Depense::sum('montant');
        
        return view('secretaire.depenses', compact('depenses', 'total_depenses'));
    }

    public function storeDepense(Request $request): RedirectResponse
    {
        $request->validate([
            'motif' => 'required|string|max:255',
            'montant' => 'required|numeric',
            'prenom_encaisseur' => 'required|string|max:255',
            'nom_encaisseur' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        Depense::create([
            'motif' => $request->motif,
            'montant' => $request->montant,
            'prenom_encaisseur' => $request->prenom_encaisseur,
            'nom_encaisseur' => $request->nom_encaisseur,
            'date' => $request->date,
        ]);

        return redirect()->route('secretaire.depenses')->with('success', 'Dépense enregistrée avec succès');
    }

    public function rapportsIntentions(): View
    {
        $date = request('date', today()->format('Y-m-d'));
        
        // Récupérer les célébrations pour la date spécifiée
        $celebrations = Celebration::where('date_celebration', $date)
            ->with('demandeMesse')
            ->orderBy('heure_celebration', 'asc')
            ->get();
        
        return view('secretaire.rapports.intentions', compact('celebrations', 'date'));
    }
}
