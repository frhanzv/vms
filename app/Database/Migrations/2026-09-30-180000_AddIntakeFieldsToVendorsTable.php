<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Fields traced directly from the real KPK "Contractor/Vendor Request" form
 * (new-port-pass-request) that the vendors table didn't have yet:
 * Type Of Registration, Payment, In/Out Bound, Name On Vendor Pass, and
 * Country/State/City (the request form only had Address 1-3 + Postcode).
 *
 * Worker Type and Location Access are NOT added here — Worker Type reuses
 * the existing `card_type` column (see VendorPassRequest's form, which now
 * collects it at intake instead of Process List assigning it later), and
 * Location Access reuses the `location_access` column already added for
 * the Card Info page.
 */
class AddIntakeFieldsToVendorsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('vendors', [
            'type_of_registration' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'type_of_application',
            ],
            'payment' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'type_of_registration',
            ],
            'in_out_bound' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'payment',
            ],
            'name_on_vendor_pass' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'full_name',
            ],
            'country' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'address_3',
            ],
            'state' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'country',
            ],
            'city' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'state',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('vendors', [
            'type_of_registration', 'payment', 'in_out_bound',
            'name_on_vendor_pass', 'country', 'state', 'city',
        ]);
    }
}
