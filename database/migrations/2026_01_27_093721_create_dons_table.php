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
        Schema::create('dons', function (Blueprint $table) {
            $table->id();
            $table->string('donateur')->nullable();
            $table->string('type_don')->default('DON'); // DON, DIME, COLLECTE, etc
            $table->decimal('montant', 10, 2);
            $table->text('description')->nullable();
            $table->date('date_don');
            $table->string('mode_paiement')->default('ESPECES'); // ESPECES, CHEQUE, VIREMENT, etc
            $table->string('statut')->default('VALIDE'); // VALIDE, ANNULE
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dons');
    }
};
