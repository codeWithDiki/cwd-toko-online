<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = UserRole::cases();

        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::createOrFirst(['name' => $role->value]);
        }
    }
}
