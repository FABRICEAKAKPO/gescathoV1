@extends('layout')

@section('title', 'Gestion des Dons')
@section('page-title', 'Gestion des Dons')

@section('content')

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
    {{ session('success') }}
</div>
@endif

<!-- Statistiques -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-center">
            <p class="text-sm text-gray-600">Total Entrées</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($total_entrees, 0, ',', ' ') }} FCFA</p>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-center">
            <p class="text-sm text-gray-600">Total Sorties</p>
            <p class="text-2xl font-bold text-red-600">{{ number_format($total_sorties, 0, ',', ' ') }} FCFA</p>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-center">
            <p class="text-sm text-gray-600"></p>
            <p class="text-2xl font-bold {{ $bilan >= 0 ? 'text-blue-600' : 'text-red-600' }}">{{ number_format($bilan, 0, ',', ' ') }} FCFA</p>
        </div>
    </div>
    
    <!-- @if($donParType->count() > 0) -->
    <!-- <div class="bg-white rounded-lg shadow p-4">
        <div class="text-center">
            <p class="text-sm text-gray-600">Types de Dons</p>
            <p class="text-2xl font-bold text-purple-600">{{ $donParType->count() }}</p>
        </div>
    </div> -->
    <!-- @endif -->
</div>

<!-- Boutons d'action -->
<div class="mb-6 flex space-x-3 flex-wrap">
    <button onclick="document.getElementById('modal-add').classList.remove('hidden')" 
            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
        <i class="fas fa-plus mr-2"></i>Enregistrer un Don
    </button>
    <button onclick="document.getElementById('modal-add-depense').classList.remove('hidden')" 
            class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg">
        <i class="fas fa-minus mr-2"></i>Ajouter une Dépense
    </button>
    
    <a href="{{ route('dons.caisse') }}" 
       class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg">
        <i class="fas fa-cash-register mr-2"></i>Caisse
    </a>

    <a href="{{ route('dons.rapport-simple') }}" 
       class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
        <i class="fas fa-file-pdf mr-2"></i>Rapport
    </a>

    <a href="{{ route('dons.rapport') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
        <i class="fas fa-chart-bar mr-2"></i>Statistiques
    </a>
</div>

<!-- Mouvements de Caisse de Dons -->
<div class="bg-white rounded-lg shadow overflow-hidden mt-6">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Mouvements de Caisse</h3>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Détail</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responsable</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($mouvementsPaginated as $mouvement)
            <tr class="{{ $mouvement->type === 'ENTREE' ? 'bg-green-50' : 'bg-red-50' }}">
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($mouvement->date)->format('d/m/Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded font-semibold {{ $mouvement->type === 'ENTREE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $mouvement->type }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm">{{ $mouvement->detail ?? '-' }}</td>
                <td class="px-6 py-4 text-sm">{{ $mouvement->nom ?? 'Anonyme' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right font-semibold {{ $mouvement->type === 'ENTREE' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $mouvement->type === 'ENTREE' ? '+' : '-' }}{{ number_format($mouvement->montant, 0, ',', ' ') }} FCFA
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                    @if($mouvement->type === 'ENTREE')
                        @if($mouvement->canBeEdited())
                            <a href="{{ route('dons.edit', $mouvement->id) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <button onclick="confirmDelete({{ $mouvement->id }})" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        @else
                            <span class="text-gray-400 text-xs">Délai dépassé</span>
                        @endif
                    @else
                        @if($mouvement->canBeEdited())
                            <a href="{{ route('dons.depense.edit', $mouvement->id) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <button onclick="confirmDeleteDepense({{ $mouvement->id }})" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        @else
                            <span class="text-gray-400 text-xs">Délai dépassé</span>
                        @endif
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun mouvement enregistré</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination mouvements -->
@if($mouvementsPaginated->hasPages())
<div class="mt-6">
    {{ $mouvementsPaginated->links() }}
</div>
@endif
<!-- Modal Ajouter Don -->
<div id="modal-add" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-2xl mx-4 max-h-screen overflow-y-auto">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold">Enregistrer un Don</h3>
                <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form action="{{ route('dons.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Donateur</label>
                    <input type="text" name="donateur" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    <small class="text-gray-500">(Laisser vide pour anonyme)</small>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type de Don</label>
                    <select name="type_don" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Sélectionner...</option>
                        <option value="DON">Don</option>
                        <option value="DIME">Dîme</option>
                        <option value="COLLECTE">Collecte</option>
                        <option value="OFFRANDE">Offrande</option>
                        <option value="QUETE">Quête</option>
                        <option value="AUTRE">Autre</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Montant (FCFA)</label>
                    <input type="number" name="montant" step="0.01" min="0" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date du Don</label>
                    <input type="date" name="date_don" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mode de Paiement</label>
                    <select name="mode_paiement" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Sélectionner...</option>
                        <option value="ESPECES">Espèces</option>
                        <option value="CHEQUE">Chèque</option>
                        <option value="VIREMENT">Virement</option>
                        <option value="MOBILE_MONEY">Mobile Money</option>
                        <option value="AUTRE">Autre</option>
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ajouter Dépense de Don -->
<div id="modal-add-depense" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-2xl mx-4 max-h-screen overflow-y-auto">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold">Enregistrer une Dépense de Don</h3>
                <button onclick="document.getElementById('modal-add-depense').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form action="{{ route('dons.depense.store') }}" id="form-depense-don" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motif</label>
                    <input type="text" name="motif" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                           placeholder="Entrez le motif de la dépense">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Montant (FCFA)</label>
                    <input type="number" name="montant" step="0.01" min="0" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" name="date_depense" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prénom Responsable</label>
                    <input type="text" name="prenom_responsable" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom Responsable</label>
                    <input type="text" name="nom_responsable" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('modal-add-depense').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
<script>
function getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce don ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/dons/' + id;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = getCSRFToken();
        
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        
        form.appendChild(csrf);
        form.appendChild(method);
        document.body.appendChild(form);
        form.submit();
    }
}

function confirmDeleteDepense(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette dépense de don ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/dons/depense/' + id;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = getCSRFToken();
        
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        
        form.appendChild(csrf);
        form.appendChild(method);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

@endsection
