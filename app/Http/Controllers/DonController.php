<?php

namespace App\Http\Controllers;

use App\Models\Don;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DonController extends Controller
{
    public function index(): View
    {
        $dons = Don::with('depenses_dons')->orderBy('date_don', 'desc')
            ->paginate(20);
        
        // Calcul des entrées et sorties
        $total_entrees = Don::where('statut', 'VALIDE')->sum('montant');
        $total_sorties = \App\Models\DepenseDon::where('statut', 'VALIDE')->sum('montant');
        $bilan = $total_entrees - $total_sorties;
        
        // Statistiques par type de don
        $donParType = Don::where('statut', 'VALIDE')
            ->selectRaw('type_don, COUNT(*) as count, SUM(montant) as total')
            ->groupBy('type_don')
            ->get();
        
        // Statistiques par mode de paiement
        $donParModePaiement = Don::where('statut', 'VALIDE')
            ->selectRaw('mode_paiement, COUNT(*) as count, SUM(montant) as total')
            ->groupBy('mode_paiement')
            ->get();
        
        // Récupérer tous les mouvements (dons et dépenses) pour affichage
        $mouvements = collect();
        
        $dons_mouvements = Don::where('statut', 'VALIDE')
            ->selectRaw('id, donateur as nom, montant, date_don as date, "ENTREE" as type, type_don as detail')
            ->orderBy('date_don', 'desc')
            ->get();
        
        $depenses_mouvements = \App\Models\DepenseDon::where('statut', 'VALIDE')
            ->selectRaw('id, CONCAT(COALESCE(nom_responsable, ""), " ", COALESCE(prenom_responsable, "")) as nom, montant, date_depense as date, "SORTIE" as type, motif as detail')
            ->orderBy('date_depense', 'desc')
            ->get();
        
        $mouvements = collect()
            ->merge($dons_mouvements)
            ->merge($depenses_mouvements)
            ->sortByDesc('date')
            ->values();
        
        // Paginer les mouvements
        $page = request()->get('page_mouvements', 1);
        $perPage = 10;
        $mouvementsPages = $mouvements->forPage($page, $perPage);
        $mouvementsPaginated = new \Illuminate\Pagination\Paginator(
            $mouvementsPages->values(),
            $perPage,
            $page,
            [
                'path' => route('dons.index'),
                'query' => request()->query(),
                'pageName' => 'page_mouvements',
            ]
        );
        
        return view('dons.index', compact('dons', 'total_entrees', 'total_sorties', 'bilan', 'donParType', 'donParModePaiement', 'mouvementsPaginated'));
    }
    
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'donateur' => 'nullable|string|max:255',
            'type_don' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date_don' => 'required|date',
            'mode_paiement' => 'required|string|max:255',
        ]);

        $don = Don::create([
            'donateur' => $request->donateur,
            'type_don' => $request->type_don,
            'montant' => $request->montant,
            'description' => $request->description,
            'date_don' => $request->date_don,
            'mode_paiement' => $request->mode_paiement,
            'statut' => 'VALIDE',
        ]);

        ActivityLogger::logCreate(Don::class, $don->id, $don->toArray());

        return redirect()->route('dons.index')->with('success', 'Don enregistré avec succès');
    }
    
    public function destroy($id): RedirectResponse
    {
        $don = Don::findOrFail($id);
        // Vérifier la restriction de 10 minutes seulement pour les non-admin
        if (auth()->user()->role !== 'admin' && !$don->canBeEdited()) {
            abort(403, 'Ce don ne peut plus être supprimé (délai de 10 minutes dépassé)');
        }
        
        ActivityLogger::logDelete(Don::class, $don->id, $don->toArray());
        
        $don->delete();
        
        return redirect()->route('dons.index')->with('success', 'Don supprimé avec succès');
    }

    public function edit($id): View
    {
        $don = Don::findOrFail($id);
        // Vérifier la restriction de 10 minutes seulement pour les non-admin
        if (auth()->user()->role !== 'admin' && !$don->canBeEdited()) {
            abort(403, 'Ce don ne peut plus être modifié (délai de 10 minutes dépassé)');
        }
        return view('dons.edit', compact('don'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $don = Don::findOrFail($id);
        // Vérifier la restriction de 10 minutes seulement pour les non-admin
        if (auth()->user()->role !== 'admin' && !$don->canBeEdited()) {
            abort(403, 'Ce don ne peut plus être modifié (délai de 10 minutes dépassé)');
        }

        $oldValues = $don->toArray();

        $request->validate([
            'donateur' => 'nullable|string|max:255',
            'type_don' => 'required|in:DON,DIME,COLLECTE,OFFRANDE,QUETE,AUTRE',
            'montant' => 'required|numeric|min:0',
            'date_don' => 'required|date',
            'mode_paiement' => 'required|in:ESPECES,CHEQUE,VIREMENT,MOBILE_MONEY,AUTRE',
            'description' => 'nullable|string',
        ]);

        $don->update([
            'donateur' => $request->donateur,
            'type_don' => $request->type_don,
            'montant' => $request->montant,
            'date_don' => $request->date_don,
            'mode_paiement' => $request->mode_paiement,
            'description' => $request->description,
        ]);

        ActivityLogger::logUpdate(Don::class, $don->id, $oldValues, $don->refresh()->toArray());

        return redirect()->route('dons.index')->with('success', 'Don modifié avec succès');
    }
    
    public function storeDepenseDon(Request $request): RedirectResponse
    {
        $request->validate([
            'motif' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date_depense' => 'required|date',
            'nom_responsable' => 'nullable|string|max:255',
            'prenom_responsable' => 'nullable|string|max:255',
        ]);

        $depense = \App\Models\DepenseDon::create([
            'don_id' => null,
            'motif' => $request->motif,
            'montant' => $request->montant,
            'description' => $request->description,
            'date_depense' => $request->date_depense,
            'nom_responsable' => $request->nom_responsable,
            'prenom_responsable' => $request->prenom_responsable,
            'statut' => 'VALIDE',
        ]);

        ActivityLogger::logCreate(\App\Models\DepenseDon::class, $depense->id, $depense->toArray());

        return redirect()->route('dons.index')->with('success', 'Dépense de don enregistrée avec succès');
    }
    
    public function destroyDepenseDon($id): RedirectResponse
    {
        $depenseDon = \App\Models\DepenseDon::findOrFail($id);
        // Vérifier la restriction de 10 minutes seulement pour les non-admin
        if (auth()->user()->role !== 'admin' && !$depenseDon->canBeEdited()) {
            abort(403, 'Cette dépense ne peut plus être supprimée (délai de 10 minutes dépassé)');
        }
        
        ActivityLogger::logDelete(\App\Models\DepenseDon::class, $depenseDon->id, $depenseDon->toArray());
        $depenseDon->delete();
        
        return redirect()->route('dons.index')->with('success', 'Dépense de don supprimée avec succès');
    }

    public function editDepenseDon($id): View
    {
        $depense = \App\Models\DepenseDon::findOrFail($id);
        // Vérifier la restriction de 10 minutes seulement pour les non-admin
        if (auth()->user()->role !== 'admin' && !$depense->canBeEdited()) {
            abort(403, 'Cette dépense ne peut plus être modifiée (délai de 10 minutes dépassé)');
        }
        return view('dons.edit-depense', compact('depense'));
    }

    public function updateDepenseDon(Request $request, $id): RedirectResponse
    {
        $depense = \App\Models\DepenseDon::findOrFail($id);
        // Vérifier la restriction de 10 minutes seulement pour les non-admin
        if (auth()->user()->role !== 'admin' && !$depense->canBeEdited()) {
            abort(403, 'Cette dépense ne peut plus être modifiée (délai de 10 minutes dépassé)');
        }

        $request->validate([
            'motif' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date_depense' => 'required|date',
            'nom_responsable' => 'nullable|string|max:255',
            'prenom_responsable' => 'nullable|string|max:255',
        ]);

        $depense = \App\Models\DepenseDon::findOrFail($id);
        $oldValues = $depense->toArray();
        
        $depense->update([
            'motif' => $request->motif,
            'montant' => $request->montant,
            'description' => $request->description,
            'date_depense' => $request->date_depense,
            'nom_responsable' => $request->nom_responsable,
            'prenom_responsable' => $request->prenom_responsable,
        ]);

        ActivityLogger::logUpdate(\App\Models\DepenseDon::class, $depense->id, $oldValues, $depense->refresh()->toArray());

        return redirect()->route('dons.index')->with('success', 'Dépense de don modifiée avec succès');
    }
    
    public function rapport(): View
    {
        // Totaux
        $total_entrees = Don::where('statut', 'VALIDE')->sum('montant');
        $total_sorties = \App\Models\DepenseDon::where('statut', 'VALIDE')->sum('montant');
        $bilan = $total_entrees - $total_sorties;
        
        // Dons par type
        $donParType = Don::where('statut', 'VALIDE')
            ->selectRaw('type_don, COUNT(*) as count, SUM(montant) as total')
            ->groupBy('type_don')
            ->orderBy('total', 'desc')
            ->get();
        
        // Dons par mode de paiement
        $donParModePaiement = Don::where('statut', 'VALIDE')
            ->selectRaw('mode_paiement, COUNT(*) as count, SUM(montant) as total')
            ->groupBy('mode_paiement')
            ->orderBy('total', 'desc')
            ->get();
        
        // Évolution mensuelle des entrées
        $entreeParMois = Don::where('statut', 'VALIDE')
            ->selectRaw('DATE_FORMAT(date_don, "%Y-%m") as mois, COUNT(*) as count, SUM(montant) as total')
            ->groupBy('mois')
            ->orderBy('mois', 'desc')
            ->limit(12)
            ->get();
        
        // Dépenses par mois
        $sortieParMois = \App\Models\DepenseDon::where('statut', 'VALIDE')
            ->selectRaw('DATE_FORMAT(date_depense, "%Y-%m") as mois, COUNT(*) as count, SUM(montant) as total')
            ->groupBy('mois')
            ->orderBy('mois', 'desc')
            ->limit(12)
            ->get();
        
        // Top donateurs
        $donParDonateur = Don::where('statut', 'VALIDE')
            ->whereNotNull('donateur')
            ->selectRaw('donateur, COUNT(*) as count, SUM(montant) as total')
            ->groupBy('donateur')
            ->orderBy('total', 'desc')
            ->limit(20)
            ->get();
        
        // Dépenses par motif
        $sortieParMotif = \App\Models\DepenseDon::where('statut', 'VALIDE')
            ->selectRaw('motif, COUNT(*) as count, SUM(montant) as total')
            ->groupBy('motif')
            ->orderBy('total', 'desc')
            ->get();
        
        return view('dons.rapport', compact('total_entrees', 'total_sorties', 'bilan', 'donParType', 'donParModePaiement', 'entreeParMois', 'sortieParMois', 'donParDonateur', 'sortieParMotif'));
    }
    
    public function caisse(): View
    {
        // Totaux
        $total_entrees = Don::where('statut', 'VALIDE')->sum('montant');
        $total_sorties = \App\Models\DepenseDon::where('statut', 'VALIDE')->sum('montant');
        $bilan = $total_entrees - $total_sorties;
        
        // Récupérer tous les mouvements de caisse (dons et dépenses) triés par date
        $dons = Don::where('statut', 'VALIDE')
            ->selectRaw('id, donateur as nom, montant, date_don as date, "ENTREE" as type, type_don as detail')
            ->orderBy('date_don', 'desc');
        
        $depenses = \App\Models\DepenseDon::where('statut', 'VALIDE')
            ->selectRaw('id, CONCAT(COALESCE(nom_responsable, ""), " ", COALESCE(prenom_responsable, "")) as nom, montant, date_depense as date, "SORTIE" as type, motif as detail');
        
        // Fusion et tri des mouvements
        $mouvements = collect()
            ->merge($dons->get())
            ->merge($depenses->get())
            ->sortByDesc('date')
            ->values();
        
        // Solde courant calculé progressivement (du plus récent au plus ancien)
        $soldeActuel = $total_entrees - $total_sorties;
        $mouvementsAvecSolde = [];
        
        foreach ($mouvements as $mouvement) {
            $mouvementsAvecSolde[] = $mouvement;
        }
        
        // Calculer le solde pour chaque mouvement (en remontant du plus récent)
        $solde = $soldeActuel;
        foreach ($mouvementsAvecSolde as &$mouvement) {
            $mouvement->solde = $solde;
            if ($mouvement->type === 'ENTREE') {
                $solde -= $mouvement->montant;
            } else {
                $solde += $mouvement->montant;
            }
        }
        
        // Évolution mensuelle
        $evolutionMensuelle = [];
        $allMois = collect()
            ->merge(Don::where('statut', 'VALIDE')
                ->selectRaw('DATE_FORMAT(date_don, "%Y-%m") as mois')
                ->distinct()
                ->pluck('mois'))
            ->merge(\App\Models\DepenseDon::where('statut', 'VALIDE')
                ->selectRaw('DATE_FORMAT(date_depense, "%Y-%m") as mois')
                ->distinct()
                ->pluck('mois'))
            ->unique()
            ->sort()
            ->reverse()
            ->values();
        
        foreach ($allMois as $mois) {
            $entrees = Don::where('statut', 'VALIDE')
                ->whereRaw('DATE_FORMAT(date_don, "%Y-%m") = ?', [$mois])
                ->sum('montant');
            
            $sorties = \App\Models\DepenseDon::where('statut', 'VALIDE')
                ->whereRaw('DATE_FORMAT(date_depense, "%Y-%m") = ?', [$mois])
                ->sum('montant');
            
            $evolutionMensuelle[] = (object)[
                'mois' => $mois,
                'entrees' => $entrees,
                'sorties' => $sorties,
                'bilan' => $entrees - $sorties,
            ];
        }
        
        return view('dons.caisse', compact('total_entrees', 'total_sorties', 'bilan', 'mouvementsAvecSolde', 'evolutionMensuelle'));
    }
    
    public function rapportSimple(): View
    {
        $dateDebut = request('date_debut') ? new \DateTime(request('date_debut')) : \Carbon\Carbon::now()->startOfMonth();
        $dateFin = request('date_fin') ? new \DateTime(request('date_fin')) : \Carbon\Carbon::now();
        
        $dateDebutStr = $dateDebut->format('Y-m-d');
        $dateFinStr = $dateFin->format('Y-m-d');
        
        // Totaux généraux
        $total_entrees = Don::where('statut', 'VALIDE')
            ->whereBetween('date_don', [$dateDebutStr, $dateFinStr])
            ->sum('montant');
        
        $total_sorties = \App\Models\DepenseDon::where('statut', 'VALIDE')
            ->whereBetween('date_depense', [$dateDebutStr, $dateFinStr])
            ->sum('montant');
        
        $solde = $total_entrees - $total_sorties;
        
        // Dons par type
        $dons_par_type = Don::where('statut', 'VALIDE')
            ->whereBetween('date_don', [$dateDebutStr, $dateFinStr])
            ->selectRaw('type_don, SUM(montant) as montant')
            ->groupBy('type_don')
            ->orderBy('montant', 'desc')
            ->get()
            ->keyBy('type_don')
            ->mapWithKeys(function ($item) {
                return [$item->type_don => $item->montant];
            });
        
        // Dépenses par motif
        $depenses_par_motif = \App\Models\DepenseDon::where('statut', 'VALIDE')
            ->whereBetween('date_depense', [$dateDebutStr, $dateFinStr])
            ->selectRaw('motif, SUM(montant) as montant')
            ->groupBy('motif')
            ->orderBy('montant', 'desc')
            ->get()
            ->keyBy('motif')
            ->mapWithKeys(function ($item) {
                return [$item->motif => $item->montant];
            });
        
        // Détails dons
        $dons = Don::where('statut', 'VALIDE')
            ->whereBetween('date_don', [$dateDebutStr, $dateFinStr])
            ->orderBy('date_don', 'desc')
            ->get();
        
        // Détails dépenses
        $depenses = \App\Models\DepenseDon::where('statut', 'VALIDE')
            ->whereBetween('date_depense', [$dateDebutStr, $dateFinStr])
            ->orderBy('date_depense', 'desc')
            ->get();
        
        return view('dons.rapport-simple', compact(
            'total_entrees', 
            'total_sorties', 
            'solde',
            'dons_par_type',
            'depenses_par_motif',
            'dons',
            'depenses',
            'dateDebut',
            'dateFin'
        ));
    }
}
