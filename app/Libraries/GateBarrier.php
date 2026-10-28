<?php

namespace App\Libraries;

/**
 * Opens a barrier through the location's ADAM I/O module, as KPK AdamServiceImpl.openGate does:
 * POST http://<adam_ip>/digitaloutput/all/value with body "DO<channel>=1", basic auth root:<adam_password>.
 * The channel is the lane's Barrier No (KPK lane number, 0 - 5).
 *
 * The ADAM password is kept encrypted (it has to be sent to the module, so it cannot be hashed).
 */
class GateBarrier
{
    private const PREFIX = 'enc:';

    public static function encryptPassword(string $plain): string
    {
        return self::PREFIX . base64_encode(service('encrypter')->encrypt($plain));
    }

    /** Null when nothing usable is stored (empty, or an old hashed password that has to be typed in again). */
    public static function decryptPassword(?string $stored): ?string
    {
        $stored = (string) $stored;
        if (! str_starts_with($stored, self::PREFIX)) {
            return null;
        }
        try {
            return service('encrypter')->decrypt(base64_decode(substr($stored, strlen(self::PREFIX)), true) ?: '');
        } catch (\Throwable $e) {
            log_message('error', 'GateBarrier: ADAM password could not be decrypted - ' . $e->getMessage());

            return null;
        }
    }

    /**
     * @param array $lane     lanes row (barrier_no)
     * @param array $location locations row (adam_ip, adam_password)
     * @return string|null    error text, or null when the module accepted the command
     */
    public static function open(array $lane, array $location): ?string
    {
        $channel = trim((string) ($lane['barrier_no'] ?? ''));
        if (! ctype_digit($channel) || (int) $channel > 5) {
            return 'Set this lane\'s Barrier No (0 - 5) under Config > Lane first.';
        }
        $ip = trim((string) ($location['adam_ip'] ?? ''));
        if ($ip === '') {
            return 'This location has no ADAM IP. Set it under Config > Location.';
        }
        $pass = self::decryptPassword($location['adam_password'] ?? null);
        if ($pass === null) {
            return 'Type the ADAM password for this location again under Config > Location.';
        }

        try {
            $res = \Config\Services::curlrequest(['timeout' => 60, 'connect_timeout' => 5, 'http_errors' => false])
                ->post('http://' . $ip . '/digitaloutput/all/value', [
                    'auth'    => ['root', $pass],
                    'headers' => ['Content-Type' => 'application/xml', 'Accept' => 'application/xml'],
                    'body'    => 'DO' . (int) $channel . '=1',
                ]);
            if ($res->getStatusCode() >= 300) {
                return 'The gate module answered ' . $res->getStatusCode() . '. The barrier was not opened.';
            }
        } catch (\Throwable $e) {
            log_message('error', 'GateBarrier: ' . $ip . ' - ' . $e->getMessage());

            return 'Could not reach the gate module at ' . $ip . '. The barrier was not opened.';
        }

        return null;
    }
}
