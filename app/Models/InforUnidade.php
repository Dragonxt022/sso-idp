<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InforUnidade extends Model
{
    use HasFactory;

    protected $table = 'infor_unidade'; // define a tabela correta

    protected $fillable = [
        'cep',
        'estado',
        'cidade',
        'bairro',
        'rua',
        'numero',
        'cnpj',
    ];

    // Campos que aparecem no JSON
    protected $appends = ['criando_em', 'ultima_atualizacao'];

    // Esconde os originais
    protected $hidden = ['created_at', 'updated_at'];

    public function getCriandoEmAttribute()
    {
        return Carbon::parse($this->created_at)->format('d/m/Y H:i:s');
    }

    public function getUltimaAtualizacaoAttribute()
    {
        return Carbon::parse($this->updated_at)->format('d/m/Y H:i:s');
    }

    public function usuarios()
    {
        return $this->hasMany(User::class, 'unidade_id');
    }
}
