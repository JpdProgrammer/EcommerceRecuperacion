<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'admin']);

        User::factory()->create([
            'name' => 'alex',
            'email' => 'alex@test.es',
            'password' => bcrypt('alex'),
        ])->assignRole('admin');

        User::factory()->create([
            'name' => 'paco',
            'email' => 'paco@test.es',
            'password' => bcrypt('paco')]);

        User::factory(100)->create();
    }
}
