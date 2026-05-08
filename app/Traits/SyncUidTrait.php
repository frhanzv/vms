<?php

namespace App\Traits;

/**
 * Provides UUID generation for the sync_uid column.
 *
 * Models using this trait must:
 *   1. Add 'sync_uid' to $allowedFields.
 *   2. Add 'assignSyncUid' to $beforeInsert.
 */
trait SyncUidTrait
{
    protected function assignSyncUid(array $data): array
    {
        if (empty($data['data']['sync_uid'])) {
            $data['data']['sync_uid'] = self::newSyncUid();
        }
        return $data;
    }

    public static function newSyncUid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
