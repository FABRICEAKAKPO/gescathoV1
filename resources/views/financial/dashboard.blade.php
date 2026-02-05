@extends('layout')

@section('title', 'Tableau de Bord Financier')
@section('page-title', 'Tableau de Bord Financier')

@section('content')
<div class="space-y-6">
    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('financial.dashboard') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                <select name="period" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="day" {{ request('period') == 'day' ? 'selected' : '' }}>Aujourd'hui</option>
                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Ce mois</option>
                    <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Cette année</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date spécifique</label>
                <input type="date" name="date" value="{{ request('date') }}" 
                       class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Filtrer
                </button>
            </div>
        </form>
    </div>
    
    <!-- Résumé financier principal -->
    <x-financial-summary 
        :user-id="null" 
        :period="$period ?? 'month'" 
        title="Résumé Financier Global"
        :show-details="true" />
    
    <!-- Si vous voulez afficher pour un utilisateur spécifique -->
    @if($userId)
        <x-financial-summary 
            :user-id="$userId" 
            :period="$period ?? 'month'" 
            title="Résumé pour l'utilisateur #{{ $userId }}"
            :show-details="true" />
    @endif
    
    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-green-600">
                {{ $financialData['recettes']->count() }}
            </div>
            <div class="text-gray-600 mt-1">Transactions de recettes</div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-red-600">
                {{ $financialData['depenses']->count() }}
            </div>
            <div class="text-gray-600 mt-1">Transactions de dépenses</div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-blue-600">
                {{ $financialData['recettes_par_type']->count() }}
            </div>
            <div class="text-gray-600 mt-1">Types de recettes</div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-orange-600">
                {{ $financialData['depenses_par_motif']->count() }}
            </div>
            <div class="text-gray-600 mt-1">Motifs de dépenses</div>
        </div>
    </div>
</div>

<script>
// Exemple d'utilisation avec AJAX
function loadFinancialData(userId = null, period = 'month') {
    fetch(`/api/financial-data?user_id=${userId}&period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Données financières:', data);
                // Mettre à jour l'interface avec les nouvelles données
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// Recharger automatiquement toutes les 5 minutes
setInterval(() => {
    loadFinancialData();
}, 300000);
</script>
@endsection