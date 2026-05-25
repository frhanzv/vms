<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CompanySeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RegistrationTypeSeeder::class);
        $this->call(BlacklistReasonSeeder::class);
        $this->call(BusinessTypeSeeder::class);
        $this->call(CountriesSeeder::class);
        $this->call(VisitorTypeSeeder::class);
        $this->call(WorkflowSeeder::class);
        $this->call(ClientFeaturesSeeder::class);
        $this->call(ClientFormFieldsSeeder::class);
        $this->call(ClientNotificationSettingsSeeder::class);
    }
}
