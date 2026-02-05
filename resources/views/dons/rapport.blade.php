@extends('layout')

@section('title', 'Rapport des Dons')
@section('page-title', 'Rapport des Dons')

@section('content')

<!-- Boutons de navigation -->
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('dons.index') }}" class="text-blue-600 hover:text-blue-900">
        <i class="fas fa-arrow-left mr-2"></i>Retour
    </a>
    <a href="{{ route('dons.caisse') }}" class="text-blue-600 hover:text-blue-900">
        <i class="fas fa-cash-register mr-2"></i>Caisse
    </a>
</div>

<!-- Résumé général -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-xl font-semibold mb-4">Résumé Financier</h3>
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
            <p class="text-gray-600">Bilan</p>
            <p class="text-3xl font-bold {{ $bilan >= 0 ? 'text-blue-600' : 'text-red-600' }}">{{ number_format($bilan, 0, ',', ' ') }} FCFA</p>
        </div>
        
        <div class="border-l-4 border-purple-600 pl-4">
            <p class="text-gray-600">Taux Utilisation</p>
            <p class="text-3xl font-bold text-purple-600">
                @if($total_entrees > 0)
                    {{ number_format(($total_sorties / $total_entrees) * 100, 2, ',', ' ') }}%
                @else
                    0%
                @endif
            </p>
        </div>
    </div>
</div>

<!-- Dons par type -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-xl font-semibold mb-4">Dons par Type</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Pourcentage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($donParType as $stat)
                <tr>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">{{ $stat->type_don }}</span>
                    </td>
                    <td class="px-4 py-2 text-right">{{ $stat->count }}</td>
                    <td class="px-4 py-2 text-right font-semibold">{{ number_format($stat->total, 0, ',', ' ') }} FCFA</td>
                    <td class="px-4 py-2 text-right">
                        @if($total_entrees > 0)
                            {{ number_format(($stat->total / $total_entrees) * 100, 2, ',', ' ') }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">Aucun don enregistré</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Dons par mode de paiement -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-xl font-semibold mb-4">Dons par Mode de Paiement</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mode</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Pourcentage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($donParModePaiement as $stat)
                <tr>
                    <td class="px-4 py-2">{{ $stat->mode_paiement }}</td>
                    <td class="px-4 py-2 text-right">{{ $stat->count }}</td>
                    <td class="px-4 py-2 text-right font-semibold">{{ number_format($stat->total, 0, ',', ' ') }} FCFA</td>
                    <td class="px-4 py-2 text-right">
                        @if($total_entrees > 0)
                            {{ number_format(($stat->total / $total_entrees) * 100, 2, ',', ' ') }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">Aucune donnée</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Évolution mensuelle -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-xl font-semibold mb-4">Évolution Mensuelle des Entrées et Sorties</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mois</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Entrées</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Sorties</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Solde</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @php
                    // Fusionner les données d'entrées et sorties
                    $allMois = [];
                    foreach($entreeParMois as $entree) {
                        $allMois[$entree->mois] = ['entree' => $entree->total, 'sortie' => 0];
                    }
                    foreach($sortieParMois as $sortie) {
                        if (!isset($allMois[$sortie->mois])) {
                            $allMois[$sortie->mois] = ['entree' => 0, 'sortie' => $sortie->total];
                        } else {
                            $allMois[$sortie->mois]['sortie'] = $sortie->total;
                        }
                    }
                    krsort($allMois);
                @endphp
                @forelse($allMois as $mois => $data)
                @php
                    $solde_mois = $data['entree'] - $data['sortie'];
                @endphp
                <tr>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::createFromFormat('Y-m', $mois)->format('F Y') }}</td>
                    <td class="px-4 py-2 text-right font-semibold text-green-600">{{ number_format($data['entree'], 0, ',', ' ') }} FCFA</td>
                    <td class="px-4 py-2 text-right font-semibold text-red-600">{{ number_format($data['sortie'], 0, ',', ' ') }} FCFA</td>
                    <td class="px-4 py-2 text-right font-semibold {{ $solde_mois >= 0 ? 'text-blue-600' : 'text-red-600' }}">{{ number_format($solde_mois, 0, ',', ' ') }} FCFA</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">Aucune donnée</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Top donateurs -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-xl font-semibold mb-4">Top 20 Donateurs</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Donateur</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($donParDonateur as $stat)
                <tr>
                    <td class="px-4 py-2">{{ $stat->donateur }}</td>
                    <td class="px-4 py-2 text-right">{{ $stat->count }}</td>
                    <td class="px-4 py-2 text-right font-semibold">{{ number_format($stat->total, 0, ',', ' ') }} FCFA</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-2 text-center text-gray-500">Aucun donateur identifié</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Dépenses par motif -->
<div class="bg-white rounded-lg shadow p-6 mt-6">
    <h3 class="text-xl font-semibold mb-4">Dépenses par Motif</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Pourcentage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($sortieParMotif as $stat)
                <tr>
                    <td class="px-4 py-2">{{ $stat->motif }}</td>
                    <td class="px-4 py-2 text-right">{{ $stat->count }}</td>
                    <td class="px-4 py-2 text-right font-semibold">{{ number_format($stat->total, 0, ',', ' ') }} FCFA</td>
                    <td class="px-4 py-2 text-right">
                        @if($total_sorties > 0)
                            {{ number_format(($stat->total / $total_sorties) * 100, 2, ',', ' ') }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">Aucune dépense enregistrée</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection