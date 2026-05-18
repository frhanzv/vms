<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(RegistrationTypeSeeder::class);
        $this->call(ClientFeaturesSeeder::class);
        $this->call(ClientFormFieldsSeeder::class);
    }
}
