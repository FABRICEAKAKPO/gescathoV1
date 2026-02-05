@props(['userId' => null, 'period' => 'month', 'title' => 'Résumé Financier', 'showDetails' => true])

@php
    use App\Services\FinanceService;
    $financeService = new FinanceService();
    $financialData = $financeService->getUserFinancialData($userId, $period);
@endphp

<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
        @if($userId)
            <p class="text-sm text-gray-500">Pour l'utilisateur #{{ $userId }}</p>
        @endif
    </div>
    
    <div class="p-6">
        <!-- Statistiques principales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Recettes -->
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-700 text-sm font-medium">Recettes</p>
                        <p class="text-2xl font-bold text-green-800">
                            {{ $financeService->formatAmount($financialData['total_recettes']) }}
                        </p>
                    </div>
                    <div class="bg-green-100 rounded-full p-2">
                        <i class="fas fa-arrow-down text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <!-- Total Dépenses -->
            <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-700 text-sm font-medium">Dépenses</p>
                        <p class="text-2xl font-bold text-red-800">
                            {{ $financeService->formatAmount($financialData['total_depenses']) }}
                        </p>
                    </div>
                    <div class="bg-red-100 rounded-full p-2">
                        <i class="fas fa-arrow-up text-red-600"></i>
                    </div>
                </div>
            </div>
            
            <!-- Solde -->
            <div class="bg-{{ $financialData['solde'] >= 0 ? 'blue' : 'orange' }}-50 rounded-lg p-4 border border-{{ $financialData['solde'] >= 0 ? 'blue' : 'orange' }}-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-{{ $financialData['solde'] >= 0 ? 'blue' : 'orange' }}-700 text-sm font-medium">Solde</p>
                        <p class="text-2xl font-bold text-{{ $financialData['solde'] >= 0 ? 'blue' : 'orange' }}-800">
                            {{ $financeService->formatAmount($financialData['solde']) }}
                        </p>
                    </div>
                    <div class="bg-{{ $financialData['solde'] >= 0 ? 'blue' : 'orange' }}-100 rounded-full p-2">
                        <i class="fas fa-{{ $financialData['solde'] >= 0 ? 'plus' : 'minus' }} text-{{ $financialData['solde'] >= 0 ? 'blue' : 'orange' }}-600"></i>
                    </div>
                </div>
            </div>
        </div>
        
        @if($showDetails)
        <!-- Détails -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Répartition des Recettes -->
            <div>
                <h4 class="text-md font-semibold text-gray-900 mb-3">Répartition des Recettes</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                    @if($financialData['recettes_par_type']->count() > 0)
                        <div class="space-y-2">
                            @foreach($financialData['recettes_par_type'] as $type => $montant)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">{{ $type }}</span>
                                    <span class="text-sm font-medium text-green-700">
                                        {{ $financeService->formatAmount($montant) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Aucune recette enregistrée</p>
                    @endif
                </div>
            </div>
            
            <!-- Répartition des Dépenses -->
            <div>
                <h4 class="text-md font-semibold text-gray-900 mb-3">Répartition des Dépenses</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                    @if($financialData['depenses_par_motif']->count() > 0)
                        <div class="space-y-2">
                            @foreach($financialData['depenses_par_motif'] as $motif => $montant)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">{{ $motif }}</span>
                                    <span class="text-sm font-medium text-red-700">
                                        {{ $financeService->formatAmount($montant) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Aucune dépense enregistrée</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Dernières transactions -->
        <div class="mt-6">
            <h4 class="text-md font-semibold text-gray-900 mb-3">Dernières Transactions</h4>
            <div class="bg-gray-50 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $transactions = collect();
                            
                            // Combiner recettes et dépenses
                            foreach($financialData['recettes'] as $recette) {
                                $transactions->push([
                                    'date' => $recette->date,
                                    'type' => 'recette',
                                    'description' => $recette->type,
                                    'montant' => $recette->montant,
                                    'model' => $recette
                                ]);
                            }
                            
                            foreach($financialData['depenses'] as $depense) {
                                $transactions->push([
                                    'date' => $depense->date,
                                    'type' => 'depense',
                                    'description' => $depense->motif,
                                    'montant' => $depense->montant,
                                    'model' => $depense
                                ]);
                            }
                            
                            // Trier par date
                            $transactions = $transactions->sortByDesc('date')->take(10);
                        @endphp
                        
                        @forelse($transactions as $transaction)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                    {{ $transaction['date']->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $transaction['type'] === 'recette' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $transaction['type'] === 'recette' ? 'Recette' : 'Dépense' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900">
                                    {{ $transaction['description'] }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-right font-medium
                                    {{ $transaction['type'] === 'recette' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $financeService->formatAmount($transaction['montant']) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                                    Aucune transaction trouvée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>