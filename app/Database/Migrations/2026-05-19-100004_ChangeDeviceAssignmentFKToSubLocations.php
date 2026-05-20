<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeDeviceAssignmentFKToSubLocations extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('device_assignments') || ! $this->db->tableExists('sub_locations')) {
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
                log_message('debug', 'ChangeDeviceAssignmentFKToSubLocations drop FK: ' . $e->getMessage());
            }
        }

        $hasSubLocFk = $this->db->query(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE '
            . 'WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME = ?',
            [$schema, 'device_assignments', 'location_id', 'sub_locations']
        )->getNumRows() > 0;

        if (! $hasSubLocFk) {
            try {
                $this->db->query(
                    'ALTER TABLE `device_assignments` '
                    . 'ADD CONSTRAINT `device_assignments_location_id_fk_sub_locations` '
                    . 'FOREIGN KEY (`location_id`) REFERENCES `sub_locations`(`id`) '
                    . 'ON DELETE SET NULL ON UPDATE CASCADE'
                );
            } catch (\Throwable $e) {
                log_message('error', 'ChangeDeviceAssignmentFKToSubLocations add FK: ' . $e->getMessage());
            }
        }

        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        // Reverting not fully supported here, as restoring to 'lanes' could cause inconsistencies
        // depending on the data. Let's just drop the sub_locations FK.
        if (! $this->db->tableExists('device_assignments')) {
            return;
        }
        $schema = $this->db->database;
        $rows   = $this->db->query(
            'SELECT DISTINCT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE '
            . 'WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME = ?',
            [$schema, 'device_assignments', 'location_id', 'sub_locations']
        )->getResultArray();

        $this->db->disableForeignKeyChecks();
        foreach ($rows as $row) {
            $name = $row['CONSTRAINT_NAME'] ?? '';
            if ($name !== '') {
                $safe = str_replace('`', '``', $name);
                try {
                    $this->db->query('ALTER TABLE `device_assignments` DROP FOREIGN KEY `' . $safe . '`');
                } catch (\Throwable $e) {
                    log_message('debug', 'ChangeDeviceAssignmentFKToSubLocations down drop FK: ' . $e->getMessage());
                }
            }
        }
        $this->db->enableForeignKeyChecks();
    }
}
