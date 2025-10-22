<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaFornecedora extends Model
{
    use HasFactory;

    protected $table = 'empresas_fornecedoras';

    protected $fillable = [
        'cnpj',
        'razao_social',
        'nome_fantasia',
        'inscricao_estadual',
        'telefone',
        'email_contato',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'logo',
    ];

    public function fornecedores()
    {
        // todos os users com empresa_fornecedora_id = id desta empresa
        return $this->hasMany(User::class, 'empresa_fornecedora_id');
    }
}

