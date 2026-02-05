@extends('layout')

@section('content')
<div class="bg-white">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Détails du log d'activité</h1>
        </div>
        <a href="{{ route('activity-logs.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            ← Retour
        </a>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informations utilisateur -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Utilisateur</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Nom</p>
                        <p class="font-medium text-gray-900">{{ $log->user_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Rôle</p>
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mt-1
                            @if($log->user_role === 'admin')
                                bg-red-100 text-red-800
                            @elseif($log->user_role === 'comptable')
                                bg-blue-100 text-blue-800
                            @else
                                bg-green-100 text-green-800
                            @endif
                        ">
                            {{ ucfirst($log->user_role) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Adresse IP</p>
                        <p class="font-medium text-gray-900 font-mono text-sm">{{ $log->ip_address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Informations action -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Action</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Type d'action</p>
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mt-1
                            @if($log->action === 'create')
                                bg-green-100 text-green-800
                            @elseif($log->action === 'update')
                                bg-yellow-100 text-yellow-800
                            @else
                                bg-red-100 text-red-800
                            @endif
                        ">
                            {{ $log->getActionLabel() }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Entité</p>
                        <p class="font-medium text-gray-900">{{ $log->model }} #{{ $log->model_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Date/Heure</p>
                        <p class="font-medium text-gray-900">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Valeurs avant/après pour les modifications -->
        @if($log->action === 'update' && ($log->old_values || $log->new_values))
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-red-50 rounded-lg p-6 border border-red-200">
                    <h3 class="text-lg font-bold text-red-900 mb-4">Valeurs avant</h3>
                    <div class="space-y-2 text-sm">
                        @if($log->old_values && is_array($log->old_values))
                            @foreach($log->old_values as $key => $value)
                                <div class="flex justify-between">
                                    <span class="text-red-700 font-medium">{{ $key }}:</span>
                                    <span class="text-red-600 font-mono">{{ is_array($value) ? json_encode($value) : $value ?? 'null' }}</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-red-600">Aucune donnée</p>
                        @endif
                    </div>
                </div>

                <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                    <h3 class="text-lg font-bold text-green-900 mb-4">Valeurs après</h3>
                    <div class="space-y-2 text-sm">
                        @if($log->new_values && is_array($log->new_values))
                            @foreach($log->new_values as $key => $value)
                                <div class="flex justify-between">
                                    <span class="text-green-700 font-medium">{{ $key }}:</span>
                                    <span class="text-green-600 font-mono">{{ is_array($value) ? json_encode($value) : $value ?? 'null' }}</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-green-600">Aucune donnée</p>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($log->action === 'create' && $log->new_values)
            <div class="mt-6 bg-green-50 rounded-lg p-6 border border-green-200">
                <h3 class="text-lg font-bold text-green-900 mb-4">Données créées</h3>
                <div class="space-y-2 text-sm">
                    @if($log->new_values && is_array($log->new_values))
                        @foreach($log->new_values as $key => $value)
                            <div class="flex justify-between">
                                <span class="text-green-700 font-medium">{{ $key }}:</span>
                                <span class="text-green-600 font-mono">{{ is_array($value) ? json_encode($value) : $value ?? 'null' }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-green-600">Aucune donnée</p>
                    @endif
                </div>
            </div>
        @elseif($log->action === 'delete' && $log->old_values)
            <div class="mt-6 bg-red-50 rounded-lg p-6 border border-red-200">
                <h3 class="text-lg font-bold text-red-900 mb-4">Données supprimées</h3>
                <div class="space-y-2 text-sm">
                    @if($log->old_values && is_array($log->old_values))
                        @foreach($log->old_values as $key => $value)
                            <div class="flex justify-between">
                                <span class="text-red-700 font-medium">{{ $key }}:</span>
                                <span class="text-red-600 font-mono">{{ is_array($value) ? json_encode($value) : $value ?? 'null' }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-red-600">Aucune donnée</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
