<?php

namespace App\Services;

/**
 * Queues immediate background sync after local data changes (debounced).
 * Works on BOTH Jetson and cloud when database.cloud points at the peer server.
 * Optional SYNC.notifyUrl — cloud pings Jetson HTTP when Jetson DB is not exposed.
 */
class SyncTrigger
{
    protected static string $dir;
    protected static string $pendingFile;
    protected static string $lastDispatchFile;

    protected static function initPaths(): void
    {
        if (isset(self::$dir)) {
            return;
        }

        self::$dir              = WRITEPATH . 'sync';
        self::$pendingFile      = self::$dir . '/pending';
        self::$lastDispatchFile = self::$dir . '/last_dispatch';
    }

    public static function isEnabled(): bool
    {
        if (! SyncService::isPeerConfigured() && empty(env('SYNC.notifyUrl'))) {
            return false;
        }

        $auto = env('SYNC.auto');

        return $auto === null || filter_var($auto, FILTER_VALIDATE_BOOLEAN);
    }

    /** Mark that local data changed — sync runs on THIS server and/or peer is notified. */
    public static function markDirty(): void
    {
        if (! self::isEnabled() || is_cli()) {
            return;
        }

        self::initPaths();
        if (! self::ensureDir()) {
            return;
        }

        if (SyncService::isPeerConfigured()) {
            self::writeStateFile(self::$pendingFile, (string) time());
        }

        // Cloud → Jetson when Jetson MySQL is not exposed: wake Jetson to pull.
        self::notifyPeerWebhook();
    }

    /**
     * After HTTP request — spawn local background sync if pending (pushes to peer DB).
     */
    public static function dispatchIfPending(): void
    {
        if (! self::isEnabled() || is_cli() || ! SyncService::isPeerConfigured()) {
            return;
        }

        self::initPaths();
        if (! self::ensureDir()) {
            return;
        }

        if (! is_file(self::$pendingFile)) {
            return;
        }

        $debounce = (int) (env('SYNC.debounceSeconds') ?? 15);
        $last     = self::readStateTimestamp(self::$lastDispatchFile);

        if (time() - $last < $debounce) {
            return;
        }

        if (! self::spawnBackgroundSync()) {
            return;
        }

        self::deleteStateFile(self::$pendingFile);
        self::writeStateFile(self::$lastDispatchFile, (string) time());
    }

    /**
     * Cloud → Jetson wake call: POST to Jetson so it pulls cloud data immediately.
     * Set SYNC.notifyUrl on cloud, SYNC.webhookToken on both servers.
     */
    public static function notifyPeerWebhook(): void
    {
        $url   = env('SYNC.notifyUrl');
        $token = env('SYNC.webhookToken');

        if (empty($url) || empty($token) || DIRECTORY_SEPARATOR === '\\') {
            return;
        }

        self::initPaths();
        if (! self::ensureDir()) {
            return;
        }

        $debounce   = (int) (env('SYNC.notifyDebounceSeconds') ?? 15);
        $notifyFile = self::$dir . '/last_notify';
        $last       = self::readStateTimestamp($notifyFile);
        if (time() - $last < $debounce) {
            return;
        }

        if (! self::writeStateFile($notifyFile, (string) time())) {
            return;
        }

        $cmd = sprintf(
            'curl -s -m 5 -X POST -H %s %s > /dev/null 2>&1 &',
            escapeshellarg('X-Sync-Token: ' . $token),
            escapeshellarg($url)
        );
        exec($cmd);
    }

    /** Run sync in a detached background process. */
    public static function spawnBackgroundSync(): bool
    {
        if (! SyncService::isPeerConfigured()) {
            return false;
        }

        self::initPaths();
        if (! self::ensureDir()) {
            return false;
        }

        $php   = env('SYNC.phpBin') ?: (defined('PHP_BINARY') ? PHP_BINARY : 'php');
        $spark = rtrim(ROOTPATH, '\\/') . DIRECTORY_SEPARATOR . 'spark';
        $log   = WRITEPATH . 'logs/sync.log';

        if (DIRECTORY_SEPARATOR === '\\') {
            return false;
        }

        $cmd = sprintf(
            '%s %s sync:run --quiet >> %s 2>&1 &',
            escapeshellarg($php),
            escapeshellarg($spark),
            escapeshellarg($log)
        );

        exec($cmd);

        return true;
    }

    protected static function ensureDir(): bool
    {
        if (! is_dir(self::$dir) && ! @mkdir(self::$dir, 0775, true) && ! is_dir(self::$dir)) {
            log_message('warning', '[SyncTrigger] Unable to create sync state directory: ' . self::$dir);
            return false;
        }

        if (! is_writable(self::$dir)) {
            log_message('warning', '[SyncTrigger] Sync state directory is not writable: ' . self::$dir);
            return false;
        }

        return true;
    }

    protected static function readStateTimestamp(string $path): int
    {
        if (! is_file($path)) {
            return 0;
        }

        $contents = @file_get_contents($path);

        return $contents === false ? 0 : (int) $contents;
    }

    protected static function writeStateFile(string $path, string $contents): bool
    {
        if (@file_put_contents($path, $contents, LOCK_EX) === false) {
            log_message('warning', '[SyncTrigger] Unable to write sync state file: ' . $path);
            return false;
        }

        return true;
    }

    protected static function deleteStateFile(string $path): void
    {
        if (is_file($path) && ! @unlink($path)) {
            log_message('warning', '[SyncTrigger] Unable to delete sync state file: ' . $path);
        }
    }
}
