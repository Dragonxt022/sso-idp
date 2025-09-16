<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audit extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'city',
    ];

    /**
     * Usuário responsável pela ação
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
