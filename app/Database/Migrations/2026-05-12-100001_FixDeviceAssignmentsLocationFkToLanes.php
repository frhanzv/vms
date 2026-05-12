<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * device_assignments.location_id stores lane ids (see DeviceAssignmentModel join).
 * Original migration pointed FK at locations; align with lanes.
 */
class FixDeviceAssignmentsLocationFkToLanes extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('device_assignments') || ! $this->db->tableExists('lanes')) {
            return;
        }

        $schema = $this->db->database;
        $rows   = $this->db->query(
            'SELECT DISTINCT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE '
            . 'WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? '
            . 'AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$schema, 'device_assignments', 'location_id']
        )->getResultArray();

        $this->db->disableForeignKeyChecks();

        foreach ($rows as $row) {
            $name = $row['CONSTRAINT_NAME'] ?? '';
            if ($name === '') {
                continue;
            }
            $safe = str_replace('`', '``', $name);
            try {
                $this->db->query('ALTER TABLE `device_assignments` DROP FOREIGN KEY `' . $safe . '`');
            } catch (\Throwable $e) {
                log_message('debug', 'FixDeviceAssignmentsLocationFkToLanes drop FK: ' . $e->getMessage());
            }
        }

        $hasLanesFk = $this->db->query(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE '
            . 'WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME = ?',
            [$schema, 'device_assignments', 'location_id', 'lanes']
        )->getNumRows() > 0;

        if (! $hasLanesFk) {
            try {
                $this->db->query(
                    'ALTER TABLE `device_assignments` '
                    . 'ADD CONSTRAINT `device_assignments_location_id_fk_lanes` '
                    . 'FOREIGN KEY (`location_id`) REFERENCES `lanes`(`id`) '
                    . 'ON DELETE SET NULL ON UPDATE CASCADE'
                );
            } catch (\Throwable $e) {
                log_message('error', 'FixDeviceAssignmentsLocationFkToLanes add FK: ' . $e->getMessage());
            }
        }

        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        // Not reverting: restoring a locations FK could break rows keyed by lane ids.
    }
}
