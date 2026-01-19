<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCitiesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'state_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('state_id', 'states', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cities');

        // Get Malaysia country ID
        $db = \Config\Database::connect();
        $malaysia = $db->table('countries')->where('code', 'MY')->get()->getRowArray();
        
        if ($malaysia) {
            $countryId = $malaysia['id'];
            
            // Get all Malaysian states
            $states = $db->table('states')->where('country_id', $countryId)->get()->getResultArray();
            $stateMap = [];
            foreach ($states as $state) {
                $stateMap[$state['code']] = $state['id'];
            }
            
            // Insert all Malaysian cities
            $cities = [
                // Johor cities
                ['state_id' => $stateMap['JHR'], 'name' => 'Johor Bahru', 'code' => 'JB', 'status' => 'active'],
                ['state_id' => $stateMap['JHR'], 'name' => 'Muar', 'code' => 'MUA', 'status' => 'active'],
                ['state_id' => $stateMap['JHR'], 'name' => 'Batu Pahat', 'code' => 'BP', 'status' => 'active'],
                ['state_id' => $stateMap['JHR'], 'name' => 'Kluang', 'code' => 'KLU', 'status' => 'active'],
                ['state_id' => $stateMap['JHR'], 'name' => 'Segamat', 'code' => 'SEG', 'status' => 'active'],
                
                // Kedah cities
                ['state_id' => $stateMap['KDH'], 'name' => 'Alor Setar', 'code' => 'ALS', 'status' => 'active'],
                ['state_id' => $stateMap['KDH'], 'name' => 'Sungai Petani', 'code' => 'SP', 'status' => 'active'],
                ['state_id' => $stateMap['KDH'], 'name' => 'Kulim', 'code' => 'KUL', 'status' => 'active'],
                ['state_id' => $stateMap['KDH'], 'name' => 'Langkawi', 'code' => 'LKW', 'status' => 'active'],
                
                // Kelantan cities
                ['state_id' => $stateMap['KTN'], 'name' => 'Kota Bharu', 'code' => 'KB', 'status' => 'active'],
                ['state_id' => $stateMap['KTN'], 'name' => 'Kuala Krai', 'code' => 'KK', 'status' => 'active'],
                ['state_id' => $stateMap['KTN'], 'name' => 'Tanah Merah', 'code' => 'TM', 'status' => 'active'],
                ['state_id' => $stateMap['KTN'], 'name' => 'Pasir Mas', 'code' => 'PM', 'status' => 'active'],
                
                // Kuala Lumpur
                ['state_id' => $stateMap['KUL'], 'name' => 'Kuala Lumpur', 'code' => 'KL', 'status' => 'active'],
                
                // Labuan
                ['state_id' => $stateMap['LBN'], 'name' => 'Victoria', 'code' => 'VIC', 'status' => 'active'],
                
                // Melaka cities
                ['state_id' => $stateMap['MLK'], 'name' => 'Melaka City', 'code' => 'MC', 'status' => 'active'],
                ['state_id' => $stateMap['MLK'], 'name' => 'Alor Gajah', 'code' => 'AG', 'status' => 'active'],
                ['state_id' => $stateMap['MLK'], 'name' => 'Jasin', 'code' => 'JAS', 'status' => 'active'],
                
                // Negeri Sembilan cities
                ['state_id' => $stateMap['NSN'], 'name' => 'Seremban', 'code' => 'SRM', 'status' => 'active'],
                ['state_id' => $stateMap['NSN'], 'name' => 'Port Dickson', 'code' => 'PD', 'status' => 'active'],
                ['state_id' => $stateMap['NSN'], 'name' => 'Nilai', 'code' => 'NIL', 'status' => 'active'],
                
                // Pahang cities
                ['state_id' => $stateMap['PHG'], 'name' => 'Kuantan', 'code' => 'KUA', 'status' => 'active'],
                ['state_id' => $stateMap['PHG'], 'name' => 'Temerloh', 'code' => 'TEM', 'status' => 'active'],
                ['state_id' => $stateMap['PHG'], 'name' => 'Bentong', 'code' => 'BTG', 'status' => 'active'],
                ['state_id' => $stateMap['PHG'], 'name' => 'Raub', 'code' => 'RAU', 'status' => 'active'],
                
                // Penang cities
                ['state_id' => $stateMap['PNG'], 'name' => 'Georgetown', 'code' => 'GT', 'status' => 'active'],
                ['state_id' => $stateMap['PNG'], 'name' => 'Butterworth', 'code' => 'BW', 'status' => 'active'],
                ['state_id' => $stateMap['PNG'], 'name' => 'Bukit Mertajam', 'code' => 'BM', 'status' => 'active'],
                ['state_id' => $stateMap['PNG'], 'name' => 'Nibong Tebal', 'code' => 'NT', 'status' => 'active'],
                
                // Perak cities
                ['state_id' => $stateMap['PRK'], 'name' => 'Ipoh', 'code' => 'IPO', 'status' => 'active'],
                ['state_id' => $stateMap['PRK'], 'name' => 'Taiping', 'code' => 'TPG', 'status' => 'active'],
                ['state_id' => $stateMap['PRK'], 'name' => 'Teluk Intan', 'code' => 'TI', 'status' => 'active'],
                ['state_id' => $stateMap['PRK'], 'name' => 'Kuala Kangsar', 'code' => 'KKG', 'status' => 'active'],
                
                // Perlis cities
                ['state_id' => $stateMap['PLS'], 'name' => 'Kangar', 'code' => 'KNG', 'status' => 'active'],
                ['state_id' => $stateMap['PLS'], 'name' => 'Arau', 'code' => 'ARA', 'status' => 'active'],
                
                // Putrajaya
                ['state_id' => $stateMap['PJY'], 'name' => 'Putrajaya', 'code' => 'PJ', 'status' => 'active'],
                
                // Sabah cities
                ['state_id' => $stateMap['SBH'], 'name' => 'Kota Kinabalu', 'code' => 'KK', 'status' => 'active'],
                ['state_id' => $stateMap['SBH'], 'name' => 'Sandakan', 'code' => 'SDK', 'status' => 'active'],
                ['state_id' => $stateMap['SBH'], 'name' => 'Tawau', 'code' => 'TWU', 'status' => 'active'],
                ['state_id' => $stateMap['SBH'], 'name' => 'Lahad Datu', 'code' => 'LD', 'status' => 'active'],
                
                // Sarawak cities
                ['state_id' => $stateMap['SWK'], 'name' => 'Kuching', 'code' => 'KCH', 'status' => 'active'],
                ['state_id' => $stateMap['SWK'], 'name' => 'Miri', 'code' => 'MIR', 'status' => 'active'],
                ['state_id' => $stateMap['SWK'], 'name' => 'Sibu', 'code' => 'SIB', 'status' => 'active'],
                ['state_id' => $stateMap['SWK'], 'name' => 'Bintulu', 'code' => 'BTU', 'status' => 'active'],
                
                // Selangor cities
                ['state_id' => $stateMap['SGR'], 'name' => 'Shah Alam', 'code' => 'SA', 'status' => 'active'],
                ['state_id' => $stateMap['SGR'], 'name' => 'Petaling Jaya', 'code' => 'PJ', 'status' => 'active'],
                ['state_id' => $stateMap['SGR'], 'name' => 'Subang Jaya', 'code' => 'SJ', 'status' => 'active'],
                ['state_id' => $stateMap['SGR'], 'name' => 'Klang', 'code' => 'KLG', 'status' => 'active'],
                ['state_id' => $stateMap['SGR'], 'name' => 'Kajang', 'code' => 'KJG', 'status' => 'active'],
                ['state_id' => $stateMap['SGR'], 'name' => 'Ampang', 'code' => 'AMP', 'status' => 'active'],
                ['state_id' => $stateMap['SGR'], 'name' => 'Rawang', 'code' => 'RWG', 'status' => 'active'],
                
                // Terengganu cities
                ['state_id' => $stateMap['TRG'], 'name' => 'Kuala Terengganu', 'code' => 'KT', 'status' => 'active'],
                ['state_id' => $stateMap['TRG'], 'name' => 'Kemaman', 'code' => 'KEM', 'status' => 'active'],
                ['state_id' => $stateMap['TRG'], 'name' => 'Dungun', 'code' => 'DUN', 'status' => 'active'],
            ];

            $db->table('cities')->insertBatch($cities);
        }
    }

    public function down()
    {
        $this->forge->dropTable('cities');
    }
}
