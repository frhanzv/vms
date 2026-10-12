<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * Card artwork used when printing passes.
 *
 * Every module has slots (front per card type, and the back). A slot prints the
 * client's active uploaded image; else the platform's (global) active upload;
 * else the built-in image in public/assets/images/template/.
 */
class CardTemplates
{
    public const SLOTS = [
        'vendor' => ['permanent' => 'Front - Permanent', 'temporary' => 'Front - Temporary / Contract', 'family' => 'Front - Family Pass', 'back' => 'Back'],
        'staff'  => ['permanent' => 'Front - Permanent', 'temporary' => 'Front - Temporary / Contract', 'back' => 'Back'],
    ];

    public const MODULES = ['vendor' => 'Vendor Pass', 'staff' => 'Staff Pass'];

    private const DEFAULTS = [
        'vendor' => ['permanent' => 'vendor_permanent.jpg', 'temporary' => 'vendor_temporary.jpg', 'family' => 'family_type.png', 'back' => 'template_back.jpg'],
        'staff'  => ['permanent' => 'staff_permanent.jpg', 'temporary' => 'staff_temporary.jpg', 'back' => 'template_back.jpg'],
    ];

    public static function isValid(string $module, string $slot): bool
    {
        return isset(self::SLOTS[$module][$slot]);
    }

    public static function defaultUrl(string $module, string $slot): string
    {
        return base_url('assets/images/template/' . (self::DEFAULTS[$module][$slot] ?? ''));
    }

    public static function urlOf(string $filePath): string
    {
        return base_url(ltrim(str_replace('\\', '/', $filePath), '/'));
    }

    /** Rows this client may see: its own and, for the platform admin, the global ones. */
    public static function rows(BaseConnection $db, ?int $clientId): array
    {
        if (! $db->tableExists('card_templates')) {
            return [];
        }
        $b = $db->table('card_templates');
        $clientId ? $b->where('client_id', $clientId) : $b->where('client_id IS NULL', null, false);

        return $b->orderBy('module')->orderBy('slot')->orderBy('id', 'DESC')->get()->getResultArray();
    }

    /** The image each slot of a module prints on: [slot => url]. */
    public static function resolveAll(BaseConnection $db, string $module, ?int $clientId): array
    {
        $out = [];
        $active = [];
        if ($db->tableExists('card_templates')) {
            $b = $db->table('card_templates')->where('module', $module)->where('is_active', 1);
            $clientId ? $b->groupStart()->where('client_id', $clientId)->orWhere('client_id IS NULL', null, false)->groupEnd() : $b->where('client_id IS NULL', null, false);
            // a client's own row beats the global one; newest first
            foreach ($b->orderBy('id', 'DESC')->get()->getResultArray() as $r) {
                $own = $clientId && (int) $r['client_id'] === $clientId;
                $key = $r['slot'];
                if (! isset($active[$key]) || ($own && ! $active[$key]['own'])) {
                    $active[$key] = ['url' => self::urlOf($r['file_path']), 'own' => $own];
                }
            }
        }
        foreach (array_keys(self::SLOTS[$module] ?? []) as $slot) {
            $out[$slot] = $active[$slot]['url'] ?? self::defaultUrl($module, $slot);
        }

        return $out;
    }
}
