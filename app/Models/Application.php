<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Application extends Model
{
    protected $fillable = ['name', 'description', 'icon', 'link_redirect'];

    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class, 'application_role'); // tabela pivot
    // }

    public function roles()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class);
    }




    protected $casts = [
        'permission_ids' => 'array', // converte JSON <-> array automaticamente
    ];
}
