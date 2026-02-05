@extends('layout')

@section('title', 'Caisse de Dons')
@section('page-title', 'Caisse de Dons')

@section('content')

<!-- Boutons de navigation -->
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('dons.index') }}" class="text-blue-600 hover:text-blue-900">
        <i class="fas fa-arrow-left mr-2"></i>Retour
    </a>
    <div class="space-x-3">
        <a href="{{ route('dons.rapport') }}" class="text-blue-600 hover:text-blue-900">
            <i class="fas fa-chart-bar mr-2"></i>Rapport
        </a>
    </div>
</div>

<!-- Résumé général -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-xl font-semibold mb-4">Résumé de Caisse</h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="border-l-4 border-green-600 pl-4">
            <p class="text-gray-600">Total Entrées</p>
            <p class="text-3xl font-bold text-green-600">{{ number_format($total_entrees, 0, ',', ' ') }} FCFA</p>
        </div>
        
        <div class="border-l-4 border-red-600 pl-4">
            <p class="text-gray-600">Total Sorties</p>
            <p class="text-3xl font-bold text-red-600">{{ number_format($total_sorties, 0, ',', ' ') }} FCFA</p>
        </div>
        
        <div class="border-l-4 {{ $bilan >= 0 ? 'border-blue-600' : 'border-red-600' }} pl-4">
            <p class="text-gray-600">Solde Caisse</p>
            <p class="text-3xl font-bold {{ $bilan >= 0 ? 'text-blue-600' : 'text-red-600' }}">{{ number_format($bilan, 0, ',', ' ') }} FCFA</p>
        </div>
        
        <div class="border-l-4 border-purple-600 pl-4">
            <p class="text-gray-600">Nombre Mouvements</p>
            <p class="text-3xl font-bold text-purple-600">{{ count($mouvementsAvecSolde) }}</p>
        </div>
    </div>
</div>

<!-- Mouvements de caisse -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-xl font-semibold mb-4">Mouvements de Caisse</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Détail</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Responsable</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Solde</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($mouvementsAvecSolde as $mouvement)
                <tr class="{{ $mouvement->type === 'ENTREE' ? 'bg-green-50' : 'bg-red-50' }}">
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($mouvement->date)->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs rounded font-semibold {{ $mouvement->type === 'ENTREE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $mouvement->type }}
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ $mouvement->detail ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $mouvement->nom ?? 'Anonyme' }}</td>
                    <td class="px-4 py-2 text-right font-semibold {{ $mouvement->type === 'ENTREE' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $mouvement->type === 'ENTREE' ? '+' : '-' }}{{ number_format($mouvement->montant, 0, ',', ' ') }} FCFA
                    </td>
                    <td class="px-4 py-2 text-right font-bold {{ $mouvement->solde >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                        {{ number_format($mouvement->solde, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-2 text-center text-gray-500">Aucun mouvement de caisse</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Évolution mensuelle -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-xl font-semibold mb-4">Évolution Mensuelle</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mois</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Entrées</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Sorties</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Bilan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($evolutionMensuelle as $evolution)
                <tr>
                    <td class="px-4 py-2 font-medium">{{ \Carbon\Carbon::parse($evolution->mois . '-01')->format('F Y') }}</td>
                    <td class="px-4 py-2 text-right text-green-600 font-semibold">+{{ number_format($evolution->entrees, 0, ',', ' ') }} FCFA</td>
                    <td class="px-4 py-2 text-right text-red-600 font-semibold">-{{ number_format($evolution->sorties, 0, ',', ' ') }} FCFA</td>
                    <td class="px-4 py-2 text-right font-bold {{ $evolution->bilan >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                        {{ number_format($evolution->bilan, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">Aucune donnée disponible</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
