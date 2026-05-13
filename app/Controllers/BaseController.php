<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * Role-based permission check.
     * superadmin can do everything. Other roles have an explicit action allowlist.
     */
    protected function userCan(string $action): bool
    {
        $role = session()->get('role') ?? 'guest';

        $map = [
            'superadmin'       => true,
            'clientsuperadmin' => ['edit', 'delete', 'export', 'approve_request', 'manage_blacklist'],
            'host'             => ['view', 'create_invitation'],
        ];

        if (!isset($map[$role])) {
            return false;
        }

        if ($map[$role] === true) {
            return true;
        }

        return in_array($action, (array) $map[$role], true);
    }

    /**
     * Whether DB has visitor type support (visitor_types table + invitations.visitor_type_id).
     * Avoids SQL errors if migrations are not applied on an environment.
     */
    protected function invitationsSupportVisitorType(): bool
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        try {
            $db = \Config\Database::connect();
            if (! $db->tableExists('visitor_types')) {
                $cached = false;

                return false;
            }
            $fields = array_map('strtolower', $db->getFieldNames('invitations'));
            $cached = in_array('visitor_type_id', $fields, true);
        } catch (\Throwable $e) {
            log_message('debug', 'invitationsSupportVisitorType: ' . $e->getMessage());
            $cached = false;
        }

        return $cached;
    }
}
