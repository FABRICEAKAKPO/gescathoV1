<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('depense_dons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('don_id');
            $table->string('motif');
            $table->decimal('montant', 10, 2);
            $table->text('description')->nullable();
            $table->date('date_depense');
            $table->string('nom_responsable')->nullable();
            $table->string('prenom_responsable')->nullable();
            $table->string('statut')->default('VALIDE'); // VALIDE, ANNULE
            $table->timestamps();
            
            $table->foreign('don_id')->references('id')->on('dons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depense_dons');
    }
};
