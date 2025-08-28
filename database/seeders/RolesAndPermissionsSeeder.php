<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Permisos base
        $permisos = [
            'tickets.crear',
            'tickets.ver',
            'tickets.asignar',
            'tickets.cambiar-estado',
            'tickets.aprobar',
            'admin.usuarios',
        ];

        foreach ($permisos as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // Roles
        $roles = [
            'operaciones'         => ['tickets.crear','tickets.ver'],
            'supervisor_control'  => ['tickets.crear','tickets.ver','tickets.asignar','tickets.cambiar-estado'],
            'coordinador_ti'      => ['tickets.ver','tickets.cambiar-estado'],
            'validador'           => ['tickets.ver','tickets.cambiar-estado'],
            'gerencia'            => ['tickets.ver','tickets.aprobar'],
            'admin'               => $permisos,
        ];

        foreach ($roles as $rol => $perms) {
            $r = Role::firstOrCreate(['name' => $rol]);
            $r->syncPermissions($perms);
        }
    }
}
