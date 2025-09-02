<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Permissões
        $permissions = [
            'Gestão de Estoque',
            'Saída de Estoque',
            'Gestão de Equipe',
            'Fluxo de Caixa',
            'DRE',
            'Contas a Pagar',
            'Gestão de Salmão',
            'Vistoria',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $roles = [
            'Franqueado',
            'Franqueadora',
            'Colaborador',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Associações (opcional - ajuste conforme necessidade)
        $roleFranqueado = Role::where('name', 'Franqueado')->first();
        $roleFranqueadora = Role::where('name', 'Franqueadora')->first();
        $roleColaborador = Role::where('name', 'Colaborador')->first();

        // Permissões para cada role
        $roleFranqueado->givePermissionTo([
            'Gestão de Estoque',
            'Saída de Estoque',
            'Gestão de Equipe',
            'Fluxo de Caixa',
            'DRE',
            'Contas a Pagar',
            'Gestão de Salmão',
            'Vistoria',
        ]);

        $roleFranqueadora->givePermissionTo([
            'Gestão de Estoque',
            'Saída de Estoque',
            'Gestão de Equipe',
            'Fluxo de Caixa',
            'DRE',
            'Contas a Pagar',
            'Gestão de Salmão',
            'Vistoria',
        ]);

        $roleColaborador->givePermissionTo([
            'Gestão de Estoque',
            'Saída de Estoque',
            'Fluxo de Caixa',
            'Contas a Pagar',
            'Gestão de Salmão',
            'Vistoria',
        ]);
    }
}
