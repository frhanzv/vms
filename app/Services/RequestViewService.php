<?php
namespace App\Services;
class RequestViewService
{
    public const SECTIONS = [
        'summary' => 'Summary cards section',
        'card_pending' => 'Card: Pending Requests', 'card_flagged' => 'Card: Flagged for Review',
        'card_expected' => 'Card: Expected Today', 'card_rejected' => 'Card: Rejected', 'photo' => 'Visitor photo', 'qr' => 'QR preview',
        'visit_details' => 'Host, arrival and purpose', 'watchlist' => 'Watchlist Screening',
        'identity' => 'ID Verification and documents', 'assets' => 'Access Control & Assets',
    ];
    public static function canEdit(string $role): bool
    {
        return in_array(strtolower(str_replace([' ', '_', '-'], '', $role)), ['admin', 'superadmin', 'clientsuperadmin'], true);
    }
    public static function normalize(array $values): array
    {
        $result = [];
        foreach (self::SECTIONS as $key => $label) $result[$key] = isset($values[$key]) ? (bool)$values[$key] : true;
        return $result;
    }
    public function get(int $clientId): array
    {
        $raw = $clientId > 0 ? (new \App\Models\SettingModel())->getSetting('request_view_client_'.$clientId) : null;
        return self::normalize(json_decode($raw ?? '', true) ?: []);
    }
    public function save(int $clientId, array $values): bool
    {
        return (bool)(new \App\Models\SettingModel())->setSetting('request_view_client_'.$clientId, json_encode(self::normalize($values)));
    }
}
