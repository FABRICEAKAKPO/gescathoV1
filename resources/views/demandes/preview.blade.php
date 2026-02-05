@extends('layout')

@section('title', 'Récapitulatif - Demande de Messe')
@section('page-title', 'Récapitulatif de la Demande')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-xl font-semibold mb-4">Vérifiez les informations avant enregistrement</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <strong>Demandeur :</strong>
            <p>{{ $data['demandeur'] ?? '' }}</p>
        </div>
        <div>
            <strong>Type de messe :</strong>
            <p>{{ $data['type_messe'] ?? '' }}</p>
        </div>
        <div class="col-span-2">
            <strong>Intentions :</strong>
            <p>{{ $data['intentions'] ?? '' }}</p>
        </div>
        <div>
            <strong>Nombre de fois :</strong>
            <p>{{ $data['nombre'] ?? '' }}</p>
        </div>
        <div>
            <strong>Montant payé :</strong>
            <p>{{ number_format($data['montant_paye'] ?? 0, 0, ',', ' ') }} FCFA</p>
        </div>
        <div>
            <strong>Date de célébration :</strong>
            <p>{{ $data['date_celebration'] ?? '' }}</p>
        </div>
        <div>
            <strong>Heure :</strong>
            <p>{{ $data['heure_celebration'] ?? '' }}</p>
        </div>
    </div>

    <div class="flex justify-end space-x-3">
        <a href="{{ route('demandes.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
            Modifier
        </a>

        <form action="{{ route('demandes.store') }}" method="POST">
            @csrf
            <input type="hidden" name="demandeur" value="{{ $data['demandeur'] ?? '' }}">
            <input type="hidden" name="intentions" value="{{ $data['intentions'] ?? '' }}">
            <input type="hidden" name="type_messe" value="{{ $data['type_messe'] ?? '' }}">
            <input type="hidden" name="nombre" value="{{ $data['nombre'] ?? '' }}">
            <input type="hidden" name="montant_paye" value="{{ $data['montant_paye'] ?? '' }}">
            <input type="hidden" name="date_celebration" value="{{ $data['date_celebration'] ?? '' }}">
            <input type="hidden" name="heure_celebration" value="{{ $data['heure_celebration'] ?? '' }}">

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Confirmer et Enregistrer</button>
        </form>
    </div>
</div>
@endsection
