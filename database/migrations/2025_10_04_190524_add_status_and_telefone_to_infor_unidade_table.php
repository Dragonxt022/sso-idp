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
        Schema::table('infor_unidade', function (Blueprint $table) {
            $table->boolean('status')->default(1); // 1 = ativo, 0 = desativado
            $table->string('telefone', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('infor_unidade', function (Blueprint $table) {
            $table->dropColumn(['status', 'telefone']);
        });
    }

};
