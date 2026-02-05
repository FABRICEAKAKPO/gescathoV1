<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DepenseController extends Controller
{
    public function index(): View
    {
        $depenses = Depense::orderBy('date', 'desc')
            ->paginate(20);
        
        $total_depenses = Depense::sum('montant');
        
        return view('depenses.index', compact('depenses', 'total_depenses'));
    }
    
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'motif' => 'required|string|max:255',
            'type' => 'required|in:DEPENSE,DEPENSE_DON',
            'montant' => 'required|numeric',
            'prenom_encaisseur' => 'required|string|max:255',
            'nom_encaisseur' => 'required|string|max:255',
            'date' => 'required|date',
            'don_id' => 'nullable|exists:dons,id',
        ]);

        $depense = Depense::create([
            'motif' => $request->motif,
            'type' => $request->type,
            'montant' => $request->montant,
            'prenom_encaisseur' => $request->prenom_encaisseur,
            'nom_encaisseur' => $request->nom_encaisseur,
            'date' => $request->date,
            'don_id' => $request->type === 'DEPENSE_DON' ? $request->don_id : null,
        ]);

        ActivityLogger::logCreate(Depense::class, $depense->id, $depense->toArray());

        return redirect()->route('depenses.index')->with('success', 'Dépense enregistrée avec succès');
    }
    
    public function destroy($id): RedirectResponse
    {
        $depense = Depense::findOrFail($id);
        
        ActivityLogger::logDelete(Depense::class, $depense->id, $depense->toArray());
        $depense->delete();
        
        return redirect()->route('depenses.index')->with('success', 'Dépense supprimée avec succès');
    }
}