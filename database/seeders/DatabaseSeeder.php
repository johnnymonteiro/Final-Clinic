<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Creating the 3 roles of the app
         */
        Role::create(['name' => 'doctor']); //id = 1
        Role::create(['name' => 'admin']);  //id = 2
        Role::create(['name' => 'patient']); //id = 3


        /**
         * Create the first user (admin) of the app
         */
        User::create([
            'name' => 'Joao Monteiro',
            'email' => 'jcdm@hotmail.com',
            'password' => bcrypt('slb4ever'),
            'role_id' => '2',
            'address' => 'Chã de Marinha, São Vicente',
            'phone_number' => '9978850',
            'department' => 'Cardiology',
            'education' => 'bachelor',
            'description' => 'system admin',
            'gender' => 'male'
        ]);

        // User::factory(10)->create(); -> create 10 random users
    }
}
