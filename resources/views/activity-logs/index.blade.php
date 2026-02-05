@extends('layout')

@section('content')
<div class="bg-white">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Journaux d'activité</h1>
                <p class="mt-2 text-sm text-gray-600">Consulter toutes les actions effectuées par les utilisateurs du système</p>
            </div>
            <div class="text-sm text-gray-500">
                Dernière actualisation: <span id="last-refresh">{{ now()->format('H:i:s') }}</span>
            </div>
        </div>
    </div>

    <script>
        // Mettre à jour l'indication de temps toutes les secondes
        setInterval(function() {
            const now = new Date();
            document.getElementById('last-refresh').textContent = now.toLocaleTimeString();
        }, 1000);
    </script>

    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 font-semibold text-gray-900">Utilisateur</th>
                        <th class="px-6 py-3 font-semibold text-gray-900">Rôle</th>
                        <th class="px-6 py-3 font-semibold text-gray-900">Action</th>
                        <th class="px-6 py-3 font-semibold text-gray-900">Entité</th>
                        <th class="px-6 py-3 font-semibold text-gray-900">Date/Heure</th>
                        <th class="px-6 py-3 font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900">{{ $log->user_name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
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
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full 
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
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $log->model }} #{{ $log->model_id }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('activity-logs.show', $log->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    Détails
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                Aucun log d'activité
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
