<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    protected $fillable = [
        'message',
        'stack_trace',
        'file',
        'line',
        'exception_type',
        'url',
        'method',
        'payload',
        'ip',
        'user_agent',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
