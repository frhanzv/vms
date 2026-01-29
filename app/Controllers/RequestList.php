<?php

namespace App\Controllers;

use App\Models\InvitationModel;
use App\Models\InvitationScheduleModel;
use App\Models\VisitorLicenseModel;
use App\Models\VisitorEquipmentModel;

class RequestList extends BaseController
{
    protected $invitationModel;
    protected $scheduleModel;
    protected $licenseModel;
    protected $equipmentModel;

    public function __construct()
    {
        $this->invitationModel = new InvitationModel();
        $this->scheduleModel = new InvitationScheduleModel();
        $this->licenseModel = new VisitorLicenseModel();
        $this->equipmentModel = new VisitorEquipmentModel();
    }

    public function index()
    {
        // Get all submitted invitations with status 'Submitted'
        $submittedRequests = $this->invitationModel
            ->where('status', 'Submitted')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Format queue requests
        $queueRequests = [];
        foreach ($submittedRequests as $request) {
            $schedule = $this->scheduleModel
                ->where('invitation_id', $request['id'])
                ->orderBy('date_from', 'ASC')
                ->first();

            $timeUntilVisit = '';
            if ($schedule) {
                $visitTime = strtotime($schedule['date_from']);
                $now = time();
                $diff = $visitTime - $now;
                
                if ($diff > 0) {
                    $hours = floor($diff / 3600);
                    $minutes = floor(($diff % 3600) / 60);
                    
                    if ($hours > 0) {
                        $timeUntilVisit = $hours . 'h ' . $minutes . 'm';
                    } else {
                        $timeUntilVisit = $minutes . 'm';
                    }
                } else {
                    $timeUntilVisit = 'Now';
                }
            }

            $queueRequests[] = [
                'id' => $request['id'],
                'name' => $request['full_name'],
                'company' => $request['company'] ?? 'N/A',
                'host' => $request['invited_by'] ?? '',
                'time' => $timeUntilVisit,
                'photo' => $request['profile_photo_path'] ? base_url('uploads/' . $request['profile_photo_path']) : '',
                'initials' => strtoupper(substr($request['full_name'], 0, 2)),
                'status' => 'normal',
                'is_flagged' => false
            ];
        }

        // Get the first request as current
        $currentRequest = null;
        if (count($submittedRequests) > 0) {
            $first = $submittedRequests[0];
            $schedule = $this->scheduleModel
                ->where('invitation_id', $first['id'])
                ->orderBy('date_from', 'ASC')
                ->first();

            // Get equipment
            $equipment = $this->equipmentModel
                ->where('invitation_id', $first['id'])
                ->findAll();

            $assets = [];
            foreach ($equipment as $item) {
                $assets[] = [
                    'type' => 'inventory_2',
                    'name' => ($item['equipment_type'] ?? 'Equipment') . 
                            ($item['serial_number'] ? ' (Serial: ' . $item['serial_number'] . ')' : '')
                ];
            }

            $currentRequest = [
                'id' => 'VIS-' . $first['id'],
                'name' => $first['full_name'],
                'company' => $first['company'] ?? 'N/A',
                'host' => $first['invited_by'] ?? 'N/A',
                'arrival' => $schedule ? date('h:i A - d/m/Y', strtotime($schedule['date_from'])) : 'N/A',
                'purpose' => $first['reason'] ?? 'N/A',
                'photo' => $first['profile_photo_path'] ? base_url('uploads/' . $first['profile_photo_path']) : '',
                'government_id_image' => $first['government_id_path'] ? base_url('uploads/' . $first['government_id_path']) : '',
                'invitation_letter' => $first['invitation_letter_path'] ? base_url('uploads/' . $first['invitation_letter_path']) : '',
                'access_zones' => explode(', ', $first['location'] ?? ''),
                'assets' => $assets,
                'notes' => '',
                'watchlist_status' => 'cleared',
                'ai_match' => $first['facial_verified_at'] ? 95 : 0,
                'ic_passport' => $first['ic_passport'] ?? 'N/A',
                'contact' => $first['contact'] ?? 'N/A',
                'email' => $first['visitor_email'] ?? 'N/A',
                'vehicle' => $first['vehicle_registration'] ?? 'N/A',
                'staff_id' => $first['staff_id'] ?? 'N/A',
                'host_contact' => $first['host_contact'] ?? 'N/A',
                'company_visited' => $first['company_visited'] ?? 'N/A'
            ];
        }

        // Calculate stats
        $stats = [
            'pending' => $this->invitationModel->where('status', 'Pending')->countAllResults(),
            'flagged' => $this->invitationModel->where('status', 'Submitted')->countAllResults(),
            'expected' => $this->invitationModel->where('status', 'Approved')->countAllResults(),
            'rejected' => $this->invitationModel->where('status', 'Rejected')->countAllResults()
        ];

        $data = [
            'pageTitle' => 'Request List - SafeG',
            'stats' => $stats,
            'currentRequest' => $currentRequest,
            'queueRequests' => $queueRequests
        ];

        return view('requests/list', $data);
    }

    public function approve()
    {
        $json = $this->request->getJSON();
        $id = $json->id ?? null;

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request ID'
            ]);
        }

        try {
            // Get the invitation
            $invitation = $this->invitationModel->find($id);
            
            if (!$invitation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invitation not found'
                ]);
            }

            // Update status to Approved
            $updated = $this->invitationModel->update($id, [
                'status' => 'Approved',
                'checked_in_at' => date('Y-m-d H:i:s')
            ]);

            if ($updated) {
                // Create record in invitation_visitors table for RFID tracking
                $invitationVisitorModel = new \App\Models\InvitationVisitorModel();
                
                // Check if record already exists
                $existing = $invitationVisitorModel->where('invitation_id', $id)->first();
                
                if (!$existing) {
                    $inserted = $invitationVisitorModel->insert([
                        'invitation_id' => $id,
                        'visitor_card_id' => null,
                        'check_in_time' => null,
                        'check_out_time' => null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    
                    log_message('debug', 'Created invitation_visitor record for invitation ID: ' . $id . ', Result: ' . ($inserted ? 'Success' : 'Failed'));
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Request approved successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to approve request'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function reject()
    {
        $json = $this->request->getJSON();
        $id = $json->id ?? null;
        $reason = $json->reason ?? '';

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request ID'
            ]);
        }

        try {
            // Update status to Rejected
            $updated = $this->invitationModel->update($id, [
                'status' => 'Rejected',
                'other_reason' => $reason
            ]);

            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Request rejected successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to reject request'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function pastVisits()
    {
        $json = $this->request->getJSON();
        $icPassport = $json->ic_passport ?? null;

        if (!$icPassport) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid IC/Passport number'
            ]);
        }

        try {
            // Fetch past visits for this visitor (exclude current pending/submitted ones)
            $visits = $this->invitationModel
                ->where('ic_passport', $icPassport)
                ->whereIn('status', ['Approved', 'Rejected'])
                ->orderBy('created_at', 'DESC')
                ->limit(10)
                ->findAll();

            $formattedVisits = [];
            foreach ($visits as $visit) {
                // Get schedule for visit date
                $schedule = $this->scheduleModel
                    ->where('invitation_id', $visit['id'])
                    ->first();

                $formattedVisits[] = [
                    'id' => $visit['id'],
                    'visit_date' => $schedule ? date('d M Y', strtotime($schedule['visit_date'])) : 'N/A',
                    'status' => $visit['status'],
                    'purpose' => $visit['purpose_of_visit'],
                    'host_name' => $visit['host_name'],
                    'company_visited' => $visit['company_visited'],
                    'checked_in_at' => $visit['checked_in_at'] ? date('d M Y H:i', strtotime($visit['checked_in_at'])) : null,
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'visits' => $formattedVisits
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
