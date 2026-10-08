<?php

/**
 * Vendor company accounts ("vendor_admin" role).
 *
 * A vendor is a COMPANY (a row in `companies`, managed under Config >
 * Company Management) that deals with a CLIENT (the tenant, a row in
 * `clients`). The account created through the public Register page is
 * linked to its company through users.company_id and to its client through
 * users.client_id.
 *
 * Tenant scoping (vendors.company_id = the client) already exists across the
 * vendor module. What these helpers add is the second level: a vendor account
 * only ever sees / edits the pass records of ITS OWN company, not those of
 * other vendor companies working for the same client.
 */

if (! function_exists('is_vendor_admin')) {
    function is_vendor_admin(): bool
    {
        helper('role');
        return normalize_role_slug((string) session()->get('role')) === 'vendor_admin';
    }
}

if (! function_exists('current_vendor_company')) {
    /**
     * The `companies` row the logged-in vendor account belongs to, or null
     * when the user isn't a vendor account / the company is missing or
     * inactive (in which case callers must treat the account as having no
     * data to show).
     */
    function current_vendor_company(): ?array
    {
        static $cache = [];

        if (! is_vendor_admin()) {
            return null;
        }

        $userId = (int) session()->get('user_id');
        if ($userId <= 0) {
            return null;
        }
        if (array_key_exists($userId, $cache)) {
            return $cache[$userId];
        }

        $user = (new \App\Models\UserModel())->find($userId);
        $companyId = (int) ($user['company_id'] ?? 0);
        if ($companyId <= 0) {
            return $cache[$userId] = null;
        }

        $company = (new \App\Models\CompanyModel())
            ->where('id', $companyId)
            ->where('status', 'active')
            ->first();

        return $cache[$userId] = ($company ?: null);
    }
}

if (! function_exists('apply_vendor_company_scope')) {
    /**
     * Restricts a `vendors` query builder to the logged-in vendor account's
     * own company. No-op for everyone else (staff are scoped by client only).
     *
     * A vendor account whose company can't be resolved gets an always-false
     * condition rather than silently seeing everything.
     *
     * @param \CodeIgniter\Database\BaseBuilder $builder
     */
    function apply_vendor_company_scope($builder): void
    {
        if (! is_vendor_admin()) {
            return;
        }

        $company = current_vendor_company();
        $regNo   = trim((string) ($company['registration_no'] ?? ''));

        if ($regNo === '') {
            $builder->where('1 = 0', null, false);
            return;
        }

        $builder->where('vendor_company_reg_id', $regNo);
    }
}
