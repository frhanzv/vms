<?php

namespace App\Libraries;

/**
 * The ONLY place VMS talks to payment. The bank (FPX) logic stays in the Java app; VMS just
 * asks it to start a payment and later asks for the result. Until the Java side is connected
 * (VEHICLE_PAYMENT_URL empty in .env) `enabled()` is false and a person records the receipt by hand.
 *
 *   .env:  VEHICLE_PAYMENT_URL = https://java-host/api/vms/payments
 *          VEHICLE_PAYMENT_KEY = shared secret sent as X-Api-Key
 */
class VehiclePayment
{
    public static function enabled(): bool
    {
        return trim((string) env('VEHICLE_PAYMENT_URL', '')) !== '';
    }

    /** Ask the payment service to start a payment. @return array{ok:bool,redirect_url?:string,ref?:string,message?:string} */
    public static function start(array $vehicle, string $returnUrl): array
    {
        if (! static::enabled()) {
            return ['ok' => false, 'message' => 'Online payment is not connected yet. Record the receipt number manually.'];
        }

        return static::call('POST', '/start', [
            'order_no'   => $vehicle['app_no'],
            'amount'     => $vehicle['amount'],
            'buyer_name' => $vehicle['owner_name'],
            'return_url' => $returnUrl,
        ]);
    }

    /** Ask for the result of a payment. @return array{ok:bool,paid?:bool,receipt_no?:string,payment_type?:string,message?:string} */
    public static function status(string $ref): array
    {
        return static::enabled() ? static::call('GET', '/status/' . rawurlencode($ref)) : ['ok' => false, 'message' => 'Online payment is not connected.'];
    }

    private static function call(string $method, string $path, ?array $body = null): array
    {
        try {
            $client = \Config\Services::curlrequest(['timeout' => 20, 'http_errors' => false]);
            $opts   = ['headers' => ['X-Api-Key' => (string) env('VEHICLE_PAYMENT_KEY', ''), 'Accept' => 'application/json']];
            if ($body !== null) {
                $opts['json'] = $body;
            }
            $res  = $client->request($method, rtrim((string) env('VEHICLE_PAYMENT_URL'), '/') . $path, $opts);
            $json = json_decode((string) $res->getBody(), true);

            return is_array($json) ? $json + ['ok' => $res->getStatusCode() < 300] : ['ok' => false, 'message' => 'Unexpected reply from the payment service.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Payment service unreachable.'];
        }
    }
}
