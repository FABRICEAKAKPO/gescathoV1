@extends('layout')

@section('title', 'Rapport Dons')
@section('page-title', 'Rapport de Dons')

@section('content')
<div class="mb-6 no-print">
    <a href="{{ route('dons.index') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
        <i class="fas fa-arrow-left mr-2"></i>Retour
    </a>
</div>

<div class="mb-6 no-print">
    <form method="GET" class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Début</label>
                <input type="date" name="date_debut" value="{{ $dateDebut->format('Y-m-d') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Fin</label>
                <input type="date" name="date_fin" value="{{ $dateFin->format('Y-m-d') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type de Rapport</label>
                <select name="type_rapport" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="total" {{ request('type_rapport') == 'total' ? 'selected' : '' }}>Totaux par Type</option>
                    <option value="detail" {{ request('type_rapport') == 'detail' ? 'selected' : '' }}>Détails</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-search mr-2"></i>Générer
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow" id="rapport-dons">
    <div class="p-6 border-b">
        <div class="flex justify-between items-center">
            <div>
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-20 h-20 mx-auto mb-4">
                <h3 class="text-xl font-semibold">Rapport de Dons</h3>
                <p class="text-gray-600">
                    Période: {{ $dateDebut->format('d/m/Y') }}
                    au {{ $dateFin->format('d/m/Y') }}
                </p>
                <p class="text-gray-600">
                    Type: {{ request('type_rapport') == 'detail' ? 'Détails' : 'Totaux par Type' }}
                </p>
            </div>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg no-print">
                <i class="fas fa-print mr-2"></i>Imprimer
            </button>
        </div>
    </div>

    <div class="p-6">
        <!-- Résumé -->
        <div class="grid grid-cols-3 gap-6 mb-8">
            <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-500">
                <p class="text-sm text-gray-600">Total Dons</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($total_entrees, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-red-50 rounded-lg p-4 border-l-4 border-red-500">
                <p class="text-sm text-gray-600">Total Dépenses</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($total_sorties, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
                <p class="text-sm text-gray-600">Solde</p>
                <p class="text-2xl font-bold {{ $solde >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                    {{ number_format($solde, 0, ',', ' ') }} FCFA
                </p>
            </div>
        </div>

        @if(request('type_rapport') == 'total' || !request('type_rapport'))
            <!-- Répartition des Dons par Type -->
            <div class="mb-8">
                <h4 class="text-lg font-semibold mb-4 text-green-700">Répartition des Dons par Type</h4>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($dons_par_type as $type => $montant)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-green-600">
                                    {{ number_format($montant, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-gray-500">Aucun don</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Répartition des Dépenses par Motif -->
            <div class="mb-8">
                <h4 class="text-lg font-semibold mb-4 text-red-700">Répartition des Dépenses par Motif</h4>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($depenses_par_motif as $motif => $montant)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $motif }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-red-600">
                                    {{ number_format($montant, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-gray-500">Aucune dépense</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <!-- Détails Dons -->
            <div class="mb-8">
                <h4 class="text-lg font-semibold mb-4 text-green-700">Détail des Dons</h4>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Donateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($dons as $don)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $don->date_don->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm">{{ $don->donateur ?? 'Anonyme' }}</td>
                                <td class="px-6 py-4 text-sm">{{ $don->type_don }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-green-600">
                                    {{ number_format($don->montant, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucun don</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Détails Dépenses -->
            <div class="mb-8">
                <h4 class="text-lg font-semibold mb-4 text-red-700">Détail des Dépenses</h4>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responsable</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($depenses as $depense)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $depense->date_depense->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm">{{ $depense->motif }}</td>
                                <td class="px-6 py-4 text-sm">{{ $depense->nom_responsable ?? '-' }} {{ $depense->prenom_responsable ?? '' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-red-600">
                                    {{ number_format($depense->montant, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucune dépense</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<style media="print">
    /* Masquer la sidebar et la navigation */
    body > div:first-child {
        display: none !important;
    }
    
    /* Masquer les éléments avec la classe no-print */
    .no-print {
        display: none !important;
    }
    
    /* Styles d'impression */
    body {
        background: white !important;
        margin: 0;
        padding: 20px;
    }
    
    #rapport-dons {
        box-shadow: none !important;
        border: none !important;
    }
    
    .bg-white {
        background: white !important;
        box-shadow: none !important;
    }
    
    table {
        page-break-inside: avoid;
    }
    
    tr {
        page-break-inside: avoid;
    }
    
    /* Afficher le contenu du rapport */
    #rapport-dons > * {
        display: block !important;
    }
</style>
@endsection
