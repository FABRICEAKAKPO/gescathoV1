@extends('layout')

@section('title', 'Modifier un Don')
@section('page-title', 'Modifier un Don')

@section('content')

<div class="mb-6">
    <a href="{{ route('dons.index') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
        <i class="fas fa-arrow-left mr-2"></i>Retour
    </a>
</div>

<div class="bg-white rounded-lg shadow max-w-2xl">
    <div class="p-6 border-b">
        <h3 class="text-xl font-semibold">Modifier le Don</h3>
    </div>
    
    <form action="{{ route('dons.update', $don->id) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Donateur</label>
                <input type="text" name="donateur" value="{{ $don->donateur ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                <small class="text-gray-500">(Laisser vide pour anonyme)</small>
                @error('donateur')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type de Don</label>
                <select name="type_don" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Sélectionner...</option>
                    <option value="DON" {{ $don->type_don === 'DON' ? 'selected' : '' }}>Don</option>
                    <option value="DIME" {{ $don->type_don === 'DIME' ? 'selected' : '' }}>Dîme</option>
                    <option value="COLLECTE" {{ $don->type_don === 'COLLECTE' ? 'selected' : '' }}>Collecte</option>
                    <option value="OFFRANDE" {{ $don->type_don === 'OFFRANDE' ? 'selected' : '' }}>Offrande</option>
                    <option value="QUETE" {{ $don->type_don === 'QUETE' ? 'selected' : '' }}>Quête</option>
                    <option value="AUTRE" {{ $don->type_don === 'AUTRE' ? 'selected' : '' }}>Autre</option>
                </select>
                @error('type_don')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Montant (FCFA)</label>
                <input type="number" name="montant" value="{{ $don->montant }}" step="0.01" min="0" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('montant')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date du Don</label>
                <input type="date" name="date_don" value="{{ $don->date_don->format('Y-m-d') }}" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('date_don')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mode de Paiement</label>
                <select name="mode_paiement" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Sélectionner...</option>
                    <option value="ESPECES" {{ $don->mode_paiement === 'ESPECES' ? 'selected' : '' }}>Espèces</option>
                    <option value="CHEQUE" {{ $don->mode_paiement === 'CHEQUE' ? 'selected' : '' }}>Chèque</option>
                    <option value="VIREMENT" {{ $don->mode_paiement === 'VIREMENT' ? 'selected' : '' }}>Virement</option>
                    <option value="MOBILE_MONEY" {{ $don->mode_paiement === 'MOBILE_MONEY' ? 'selected' : '' }}>Mobile Money</option>
                    <option value="AUTRE" {{ $don->mode_paiement === 'AUTRE' ? 'selected' : '' }}>Autre</option>
                </select>
                @error('mode_paiement')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">{{ $don->description ?? '' }}</textarea>
                @error('description')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('dons.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                <i class="fas fa-save mr-2"></i>Mettre à jour
            </button>
        </div>
    </form>
</div>

@endsection
