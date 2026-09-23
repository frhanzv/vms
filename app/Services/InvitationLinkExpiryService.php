<?php

namespace App\Services;

final class InvitationLinkExpiryService
{
    /**
     * A configured expiry takes precedence. Otherwise, expire at the exact
     * end time of the final scheduled visit.
     *
     * @param array<int, array<string, mixed>> $schedules
     */
    public function resolve(?string $configuredExpiry, array $schedules = []): ?string
    {
        $configuredExpiry = trim((string) $configuredExpiry);
        if ($configuredExpiry !== '') {
            return $this->normalizeConfiguredExpiry($configuredExpiry);
        }

        $latest = null;
        foreach ($schedules as $schedule) {
            $dateTo = trim((string) ($schedule['date_to'] ?? ''));
            $timestamp = $dateTo !== '' ? strtotime($dateTo) : false;
            if ($timestamp !== false && ($latest === null || $timestamp > $latest)) {
                $latest = $timestamp;
            }
        }

        return $latest === null ? null : date('Y-m-d H:i:s', $latest);
    }

    public function isExpired(?string $expiry): bool
    {
        $timestamp = $expiry !== null ? strtotime($expiry) : false;

        return $timestamp !== false && $timestamp < time();
    }

    public function registrationUnavailableReason(?string $status, ?string $effectiveExpiry): ?string
    {
        $status = strtolower(trim((string) $status));

        if ($status === 'expired' || $this->isExpired($effectiveExpiry)) {
            return 'This invitation registration link has expired.';
        }

        if ($status === 'submitted') {
            return 'Registration has already been completed.';
        }

        if ($status === 'rejected') {
            return 'This invitation has been rejected and can no longer be registered.';
        }

        if ($status !== 'pending') {
            return 'This invitation has already been processed and can no longer be registered.';
        }

        return null;
    }

    private function normalizeConfiguredExpiry(string $value): ?string
    {
        $timestamp = strtotime($value);

        if ($timestamp === false) {
            return null;
        }

        // Host-selected expiry values come from a date-only field and are
        // stored at midnight. System-derived values contain the exact date_to
        // time and must retain it.
        if (preg_match('/\d{2}:\d{2}(?::\d{2})?/', $value) === 1
            && date('H:i:s', $timestamp) !== '00:00:00') {
            return date('Y-m-d H:i:s', $timestamp);
        }

        return date('Y-m-d 23:59:59', $timestamp);
    }
}
