<?php

namespace App\Traits;

use App\Exceptions\ConcurrencyException;

/**
 * Provides optimistic locking via a `version` column.
 *
 * Models using this trait must have a `version` INT column on their table.
 * The trait adds `version` to $allowedFields automatically and provides
 * helper methods for version-checked updates.
 */
trait OptimisticLockTrait
{
    /**
     * Update a row only if the version matches. Increments version on success.
     *
     * @param  int|string  $id       Primary key value
     * @param  array       $data     Fields to update (version is handled automatically)
     * @param  int         $version  The version the caller last read
     * @param  string      $entityType  Human-readable entity name for error messages
     * @return bool
     * @throws ConcurrencyException if the version does not match (row was modified)
     */
    public function updateWithLock($id, array $data, int $version, string $entityType = 'record'): bool
    {
        $db = \Config\Database::connect();
        $table = $this->table;
        $pk = $this->primaryKey;

        unset($data['version']);

        $data['version'] = $version + 1;
        $data['updated_at'] = date('Y-m-d H:i:s');

        $builder = $db->table($table);
        $builder->where($pk, $id);
        $builder->where('version', $version);
        $builder->update($data);

        if ($db->affectedRows() === 0) {
            $current = $db->table($table)->where($pk, $id)->get()->getRowArray();
            if (!$current) {
                throw new ConcurrencyException($entityType, $id, "This {$entityType} no longer exists.");
            }
            throw new ConcurrencyException($entityType, $id);
        }

        return true;
    }

    /**
     * Update a row with both a version check and additional WHERE conditions.
     * Useful for state-transition operations (e.g. status must be 'Submitted').
     *
     * @param  int|string  $id          Primary key value
     * @param  array       $data        Fields to update
     * @param  int         $version     Expected current version
     * @param  array       $conditions  Additional WHERE conditions ['column' => 'value']
     * @param  string      $entityType  Human-readable entity name
     * @param  string      $conflictMessage  Custom message when conditions fail
     * @return bool
     * @throws ConcurrencyException
     */
    public function updateWithLockAndConditions(
        $id,
        array $data,
        int $version,
        array $conditions,
        string $entityType = 'record',
        string $conflictMessage = ''
    ): bool {
        $db = \Config\Database::connect();
        $table = $this->table;
        $pk = $this->primaryKey;

        unset($data['version']);

        $data['version'] = $version + 1;
        $data['updated_at'] = date('Y-m-d H:i:s');

        $builder = $db->table($table);
        $builder->where($pk, $id);
        $builder->where('version', $version);
        foreach ($conditions as $col => $val) {
            $builder->where($col, $val);
        }
        $builder->update($data);

        if ($db->affectedRows() === 0) {
            $current = $db->table($table)->where($pk, $id)->get()->getRowArray();
            if (!$current) {
                throw new ConcurrencyException($entityType, $id, "This {$entityType} no longer exists.");
            }
            if ($conflictMessage !== '') {
                throw new ConcurrencyException($entityType, $id, $conflictMessage);
            }
            throw new ConcurrencyException($entityType, $id);
        }

        return true;
    }

    /**
     * Conditional update without version column — purely state-based guard.
     * Used when the caller doesn't track versions (e.g. RFID scans).
     *
     * @param  int|string  $id          Primary key value
     * @param  array       $data        Fields to update
     * @param  array       $conditions  WHERE conditions the row must satisfy
     * @param  string      $entityType  Human-readable entity name
     * @param  string      $conflictMessage  Custom message when conditions fail
     * @return bool  true if updated, false if conditions weren't met
     */
    public function updateWithConditions(
        $id,
        array $data,
        array $conditions,
        string $entityType = 'record',
        string $conflictMessage = ''
    ): bool {
        $db = \Config\Database::connect();
        $table = $this->table;
        $pk = $this->primaryKey;

        if (!isset($data['version'])) {
            $data['version'] = $db->table($table)->select('version')->where($pk, $id)->get()->getRow()->version ?? 1;
            $data['version']++;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $builder = $db->table($table);
        $builder->where($pk, $id);
        foreach ($conditions as $col => $val) {
            if ($val === null) {
                $builder->where($col);
            } else {
                $builder->where($col, $val);
            }
        }
        $builder->update($data);

        return $db->affectedRows() > 0;
    }
}
