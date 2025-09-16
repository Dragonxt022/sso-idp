<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaFornecedora extends Model
{
    use HasFactory;

    protected $table = 'empresas_fornecedoras';

    protected $fillable = [
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
