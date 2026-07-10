<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Enums\RolesEnum;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear tu usuario Admin
        $admin = User::firstOrCreate(
            ['email' => 'programador2-it@uqroo.edu.mx'],
            [
                'name' => 'Raúl Andrés De la Rosa Gamboa',
                'is_active' => 1,
            ]
        );

        // 2. CREAR EL ROL ANTES DE ASIGNARLO (¡Esta es la solución!)
        Role::firstOrCreate(['name' => RolesEnum::SUPER_ADMIN->value, 'guard_name' => 'web']);

        // 3. Ahora sí, asignarle el rol
        if (!$admin->hasRole(RolesEnum::SUPER_ADMIN->value)) {
            $admin->assignRole(RolesEnum::SUPER_ADMIN->value);
        }

        // 4. Llamar a tu seeder con los datos reales de la UQROO
        $this->call([
            UqrooCatalogSeeder::class,
            DynamicFormSeeder::class,
        ]);
    }
}
