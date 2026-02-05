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
        Schema::table('depenses', function (Blueprint $table) {
            $table->dropForeign(['don_id']);
            $table->dropColumn(['type', 'don_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('depenses', function (Blueprint $table) {
            $table->string('type')->default('DEPENSE')->after('motif');
            $table->unsignedBigInteger('don_id')->nullable()->after('type');
            $table->foreign('don_id')->references('id')->on('dons')->onDelete('set null');
        });
    }
};
