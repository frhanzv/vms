<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adds a second, independent toggle to client_form_fields: is_enabled
 * already controls whether a field/section/button shows at all; is_required
 * controls whether it's mandatory on the form. NULL means "use the field's
 * built-in default" (see ClientFormFieldModel::vendorPassFields()'s
 * 'default_required' key) — only a saved 0/1 overrides that.
 *
 * Only ClientFormFieldModel::vendorPassFields() actually declares which
 * fields are 'requirable' for now (per the Vendor module's "make everything
 * configurable" request) — other form types simply never set this column,
 * so they behave exactly as before.
 */
class AddIsRequiredToClientFormFields extends Migration
{
    public function up()
    {
        $this->db->resetDataCache();
        if (! $this->db->fieldExists('is_required', 'client_form_fields')) {
            $this->forge->addColumn('client_form_fields', [
                'is_required' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'is_enabled',
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('client_form_fields', 'is_required');
    }
}
