<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\RolePermission;

class RolePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Creamos o actualizamos los roles "administrador" y "vendedor"
        $adminRole = Role::updateOrCreate(
            ['nombre_rol' => 'administrador'],
            [] // Puedes agregar otros atributos si es necesario
        );

        $sellerRole = Role::updateOrCreate(
            ['nombre_rol' => 'vendedor'],
            [] // Puedes agregar otros atributos si es necesario
        );

        // (Opcional) Limpiar permisos existentes para estos roles
        RolePermission::whereIn('role_id', [$adminRole->id, $sellerRole->id])->delete();

        // Permisos para el rol administrador:
        // El administrador puede ver: categorías, empresas, productos, roles y usuarios.
        $adminPermissions = ['categories', 'empresas', 'productos', 'roles', 'users'];
        foreach ($adminPermissions as $resource) {
            RolePermission::create([
                'role_id'  => $adminRole->id,
                'resource' => $resource,
            ]);
        }

        // Permisos para el rol vendedor:
        // El vendedor puede ver: productos.
        $sellerPermissions = ['productos'];
        foreach ($sellerPermissions as $resource) {
            RolePermission::create([
                'role_id'  => $sellerRole->id,
                'resource' => $resource,
            ]);
        }
    }
}
