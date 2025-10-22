<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('infor_unidade', function (Blueprint $table) {
            $table->string('link_cardapio')->nullable()->after('telefone');
        });
    }

    public function down(): void
    {
        Schema::table('infor_unidade', function (Blueprint $table) {
            $table->dropColumn('link_cardapio');
        });
    }
};
