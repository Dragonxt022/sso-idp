<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('empresa_fornecedora_id')
                ->nullable()
                ->constrained('empresas_fornecedoras')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['empresa_fornecedora_id']);
            $table->dropColumn('empresa_fornecedora_id');
        });
    }
};
