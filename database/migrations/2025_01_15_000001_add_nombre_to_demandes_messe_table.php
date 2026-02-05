<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('demandes_messe', function (Blueprint $table) {
            $table->integer('nombre')->default(1)->after('type_messe');
        });
    }

    public function down()
    {
        Schema::table('demandes_messe', function (Blueprint $table) {
            $table->dropColumn('nombre');
        });
    }
};
