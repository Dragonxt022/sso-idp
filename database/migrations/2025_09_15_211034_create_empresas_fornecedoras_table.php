<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas_fornecedoras', function (Blueprint $table) {
            $table->id();

            // Relacionamento com usuário responsável (opcional)
            $table->foreignId('user_id')->nullable()
                  ->constrained()->onDelete('set null');

            $table->string('cnpj', 18)->unique();
            $table->string('razao_social');
            $table->string('nome_fantasia')->nullable();
            $table->string('inscricao_estadual')->nullable();
            $table->string('telefone')->nullable();
            $table->string('email_contato')->nullable();
            $table->text('endereco')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();
            $table->string('cep', 9)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas_fornecedoras');
    }
};
