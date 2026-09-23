<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

/**
 * Vendor Pass — carries over the field set from KPK's SAFEG_VENDOR_PASS
 * Java @Entity (see domain/master/VendorPass/VendorPass.java), trimmed to
 * what a pass request actually needs. Follow-on features (photo/urine/
 * vaccine/QR tracking) can extend this table later the same way
 * AddVisaLicenseFieldsToStaff.php extended the staff table.
 */
class CreateVendorsTable extends Migration
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

            // VMS tenancy — which client/company officer manages this record
            'company_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],

            // Application Info
            'app_no'               => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'date_of_application'  => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'type_of_application'  => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'sub_type'              => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],

            // Vendor's own company (the SSM-registered company they work for — distinct from company_id above)
            'vendor_company_reg_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'vendor_company_name'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],

            // Personal Details
            'full_name'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'ic_no'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'passport_no'  => ['type' => 'VARCHAR', 'constraint' => 16, 'null' => true],
            'dob'          => ['type' => 'DATE', 'null' => true],
            'sex'          => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'resident'     => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'contact_no'   => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'email'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'staff_no'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'designation'  => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],

            // Address
            'address_1' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'address_2' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'address_3' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'postcode'  => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],

            // Visit Details — who/where they're visiting on-site
            'name_of_person_visited'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'contact_no_of_person_visited' => ['type' => 'VARCHAR', 'constraint' => 14, 'null' => true],
            'location_visited'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],

            // CSP (Company Security Permit)
            'csp_number'      => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'csp_expiry_date' => ['type' => 'DATE', 'null' => true],

            // E-Vetting
            'evetting_date_of_application' => ['type' => 'DATE', 'null' => true],
            'evetting_date_of_result'      => ['type' => 'DATE', 'null' => true],
            'evetting_result'              => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],

            // Pass / Status
            'pass_expiry'  => ['type' => 'DATE', 'null' => true],
            'status'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'next_action'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'remark'       => ['type' => 'TEXT', 'null' => true],

            // Documents
            'photo'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'government_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'other_doc'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],

            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('company_id');
        $this->forge->addKey('ic_no');
        $this->forge->createTable('vendors', true);
    }

    public function down()
    {
        $this->forge->dropTable('vendors');
    }
}
