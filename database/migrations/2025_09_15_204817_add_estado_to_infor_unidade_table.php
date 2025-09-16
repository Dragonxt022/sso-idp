<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('infor_unidade', function (Blueprint $table) {
            $table->string('estado', 2)->nullable();
            // '2' se for sigla do estado (ex: SP), ajuste se quiser tamanho maior
        });
    }

    public function down(): void
    {
        Schema::table('infor_unidade', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
