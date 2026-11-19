<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Campus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Enums\RolesEnum;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Catálogos institucionales y formularios dinámicos
        $this->call([
            UqrooCatalogSeeder::class,
            DynamicFormSeeder::class,
        ]);

        // 2. Roles del sistema según RolesEnum
        foreach (RolesEnum::cases() as $roleEnum) {
            Role::firstOrCreate([
                'name' => $roleEnum->value,
                'guard_name' => 'web',
            ]);
        }

        // 3. Super Admin con todos los permisos (generados por shield:generate)
        $superAdminRole = Role::where('name', RolesEnum::SUPER_ADMIN->value)->first();
        $allPermissions = Permission::all();
        if ($superAdminRole && $allPermissions->isNotEmpty()) {
            $superAdminRole->syncPermissions($allPermissions);
        }

        // 4. Super administradores
        //    Formato en .env:  SUPER_ADMINS="correo1|Nombre 1,correo2|Nombre 2"
        //    Contraseña:       SUPER_ADMIN_PASSWORD=...
        $definition = env('SUPER_ADMINS', 'programador2-it@uqroo.edu.mx|Raúl Andrés De la Rosa Gamboa');
        $password = env('SUPER_ADMIN_PASSWORD', 'admin1234');
        $allCampuses = Campus::all();

        foreach (array_filter(array_map('trim', explode(',', $definition))) as $entry) {
            [$email, $name] = array_pad(array_map('trim', explode('|', $entry, 2)), 2, null);

            $admin = User::where('email', $email)->first();

            if (! $admin) {
                $admin = User::create([
                    'email' => $email,
                    'name' => $name ?: $email,
                    'password' => Hash::make($password),
                    'user_type' => 'Administrador',
                    'is_active' => true,
                ]);
            } else {
                // No sobrescribe la contraseña si el usuario ya existe
                $admin->update(['user_type' => 'Administrador', 'is_active' => true]);
            }

            if (! $admin->hasRole(RolesEnum::SUPER_ADMIN->value)) {
                $admin->assignRole(RolesEnum::SUPER_ADMIN->value);
            }

            if ($allCampuses->isNotEmpty()) {
                $admin->campuses()->sync($allCampuses->pluck('id'));
            }

            UserProfile::firstOrCreate(
                ['user_id' => $admin->id],
                [
                    'phone' => '9830000000',
                    'age' => 30,
                    'gender' => 'Masculino',
                    'sex' => 'Hombre',
                    'vulnerable_group' => 'No',
                ]
            );
        }
    }
}
