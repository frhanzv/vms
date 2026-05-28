<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardWidgetPreferenceModel extends Model
{
    protected $table         = 'dashboard_widget_preferences';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['user_id', 'widget_config'];
    protected $useTimestamps = true;

    // Fixed col-span classes per widget — the user cannot change these
    public static array $colSpans = [
        'critical-alert'        => 'col-span-2 xl:col-span-4',
        'access-denied'         => 'col-span-1 xl:col-span-2',
        'overstay-alerts'       => 'col-span-1 xl:col-span-2',
        'stat-expected'         => 'col-span-1',
        'stat-onsite'           => 'col-span-1',
        'stat-checkedout'       => 'col-span-1',
        'stat-alerts'           => 'col-span-1',
        'upcoming-appointments' => 'col-span-1 xl:col-span-2',
        'today-appointments'    => 'col-span-1 xl:col-span-2',
        'occupancy-chart'       => 'col-span-2 xl:col-span-3',
        'recent-activity'       => 'col-span-2 xl:col-span-1',
        'visitors-table'        => 'col-span-2 xl:col-span-4',
        'onsite-table'          => 'col-span-2 xl:col-span-4',
        'traffic-analytics'     => 'col-span-2 xl:col-span-4',
        'poster-banner'         => 'col-span-2 xl:col-span-4',
    ];

    public static array $defaultWidgets = [
        ['id' => 'critical-alert',        'label' => 'Security Alert',          'visible' => true, 'position' => 0],
        ['id' => 'access-denied',         'label' => 'Access Denied',           'visible' => true, 'position' => 1],
        ['id' => 'overstay-alerts',       'label' => 'Overstay Alerts',         'visible' => true, 'position' => 2],
        ['id' => 'stat-expected',         'label' => 'Expected Today',          'visible' => true, 'position' => 3],
        ['id' => 'stat-onsite',           'label' => 'Currently On-Site',       'visible' => true, 'position' => 4],
        ['id' => 'stat-checkedout',       'label' => 'Checked Out',             'visible' => true, 'position' => 5],
        ['id' => 'stat-alerts',           'label' => 'Security Alerts',         'visible' => true, 'position' => 6],
        ['id' => 'upcoming-appointments', 'label' => 'Upcoming Appointments',   'visible' => true, 'position' => 7],
        ['id' => 'today-appointments',    'label' => "Today's Appointments",    'visible' => true, 'position' => 8],
        ['id' => 'occupancy-chart',       'label' => 'Visitor Occupancy',       'visible' => true, 'position' => 9],
        ['id' => 'recent-activity',       'label' => 'Recent Activity',         'visible' => true, 'position' => 10],
        ['id' => 'visitors-table',        'label' => 'Visitors Table',          'visible' => true, 'position' => 11],
        ['id' => 'onsite-table',          'label' => 'Currently On-Site Table', 'visible' => true, 'position' => 12],
        ['id' => 'traffic-analytics',     'label' => 'Traffic Analytics',       'visible' => true,  'position' => 13],
        ['id' => 'poster-banner',         'label' => 'Poster Banner',           'visible' => false, 'position' => 14, 'image' => null],
    ];

    public function getPreferences(int $userId): array
    {
        $row = $this->where('user_id', $userId)->first();
        if (!$row || empty($row['widget_config'])) {
            return self::$defaultWidgets;
        }
        $saved    = json_decode($row['widget_config'], true) ?? [];
        $savedIds = array_column($saved, 'id');
        foreach (self::$defaultWidgets as $default) {
            if (!in_array($default['id'], $savedIds)) {
                $saved[] = $default;
            }
        }
        usort($saved, fn($a, $b) => ($a['position'] ?? 0) <=> ($b['position'] ?? 0));
        return $saved;
    }

    public function savePreferences(int $userId, array $configs): void
    {
        $json     = json_encode($configs);
        $existing = $this->where('user_id', $userId)->first();
        if ($existing) {
            $this->update($existing['id'], ['widget_config' => $json]);
        } else {
            $this->insert(['user_id' => $userId, 'widget_config' => $json]);
        }
    }
}
