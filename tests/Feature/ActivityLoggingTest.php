<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Don;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_log_created_on_don_creation()
    {
        // Créer un utilisateur admin
        $user = User::factory()->create(['role' => 'admin']);

        // Authentifier l'utilisateur
        $this->actingAs($user);

        // Créer un don
        $response = $this->post(route('dons.store'), [
            'donateur' => 'Test Donateur',
            'type_don' => 'DON',
            'montant' => 100.00,
            'date_don' => now()->format('Y-m-d'),
            'mode_paiement' => 'ESPECES',
            'description' => 'Test description',
        ]);

        // Vérifier que le log d'activité a été créé
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'create',
            'model' => Don::class,
        ]);

        // Vérifier le contenu du log
        $log = ActivityLog::where('action', 'create')->first();
        $this->assertNotNull($log);
        $this->assertEquals($user->name, $log->user_name);
        $this->assertEquals('admin', $log->user_role);
    }

    public function test_activity_log_created_on_don_update()
    {
        // Créer un utilisateur admin
        $user = User::factory()->create(['role' => 'admin']);

        // Créer un don
        $don = Don::factory()->create();

        // Authentifier l'utilisateur
        $this->actingAs($user);

        // Modifier le don
        $response = $this->put(route('dons.update', $don->id), [
            'donateur' => 'Nouveau donateur',
            'type_don' => 'DIME',
            'montant' => 200.00,
            'date_don' => now()->format('Y-m-d'),
            'mode_paiement' => 'CHEQUE',
            'description' => 'Nouvelle description',
        ]);

        // Vérifier que le log d'activité a été créé
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'update',
            'model' => Don::class,
        ]);
    }

    public function test_activity_log_created_on_don_deletion()
    {
        // Créer un utilisateur admin
        $user = User::factory()->create(['role' => 'admin']);

        // Créer un don
        $don = Don::factory()->create();

        // Authentifier l'utilisateur
        $this->actingAs($user);

        // Supprimer le don
        $response = $this->delete(route('dons.destroy', $don->id));

        // Vérifier que le log d'activité a été créé
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'delete',
            'model' => Don::class,
        ]);
    }

    public function test_admin_can_view_activity_logs()
    {
        // Créer un utilisateur admin
        $admin = User::factory()->create(['role' => 'admin']);

        // Créer un log d'activité
        ActivityLog::create([
            'user_id' => $admin->id,
            'user_name' => $admin->name,
            'user_role' => 'admin',
            'action' => 'create',
            'model' => Don::class,
            'model_id' => 1,
        ]);

        // Authentifier l'utilisateur admin
        $this->actingAs($admin);

        // Accéder à la page des logs
        $response = $this->get(route('activity-logs.index'));

        // Vérifier que la page est accessible
        $response->assertStatus(200);
        $response->assertViewHas('logs');
    }

    public function test_non_admin_cannot_view_activity_logs()
    {
        // Créer un utilisateur non-admin
        $user = User::factory()->create(['role' => 'comptable']);

        // Authentifier l'utilisateur
        $this->actingAs($user);

        // Tenter d'accéder à la page des logs
        $response = $this->get(route('activity-logs.index'));

        // Vérifier que l'accès est refusé
        $response->assertStatus(403);
    }
}
