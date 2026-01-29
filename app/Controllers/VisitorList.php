<?php

namespace App\Controllers;

use App\Models\InvitationModel;
use App\Models\InvitationVisitorModel;
use App\Models\VisitorCardModel;

class VisitorList extends BaseController
{
    protected $invitationModel;
    protected $invitationVisitorModel;
    protected $visitorCardModel;

    public function __construct()
    {
        $this->invitationModel = new InvitationModel();
        $this->invitationVisitorModel = new InvitationVisitorModel();
        $this->visitorCardModel = new VisitorCardModel();
    }

    public function index()
    {
        // Get all approved invitations with card details
        $db = \Config\Database::connect();
        $builder = $db->table('invitation_visitors iv');
        $builder->select('iv.*, 
                          i.full_name as visitor_name, 
                          i.ic_passport as visitor_ic_passport,
                          i.contact as visitor_contact,
                          i.company as visitor_company,
                          i.invited_by as host_name,
                          i.reason as visit_purpose,
                          i.vehicle_registration as vehicle_reg,
                          i.location,
                          i.created_at,
                          vc.card_id as card_epc,
                          vc.status as card_status');
        $builder->join('invitations i', 'i.id = iv.invitation_id');
        $builder->join('visitor_cards vc', 'vc.id = iv.visitor_card_id', 'left');
        $builder->where('i.status', 'Approved');
        // Show today's visitors OR visitors approved today
        $builder->groupStart();
        $builder->where('DATE(i.created_at)', date('Y-m-d'))
                ->orWhere('DATE(i.updated_at)', date('Y-m-d'));
        $builder->groupEnd();
        $builder->orderBy('i.created_at', 'DESC');
        
        // Debug: Log the query
        $sql = $builder->getCompiledSelect(false);
        log_message('debug', 'Visitor List Query: ' . $sql);
        
        $results = $builder->get()->getResultArray();
        
        // Debug: Log results count
        log_message('debug', 'Visitor List Results Count: ' . count($results));
        
        // Also check total approved invitations
        $totalInvitations = $db->table('invitations')->where('status', 'Approved')->countAllResults();
        log_message('debug', 'Total Approved Invitations: ' . $totalInvitations);
        
        // Check invitation_visitors records
        $totalIV = $db->table('invitation_visitors')->countAllResults();
        log_message('debug', 'Total invitation_visitors Records: ' . $totalIV);

        // Count statistics
        $totalApproved = count($results);
        $checkedIn = 0;
        $withCard = 0;
        
        foreach ($results as $row) {
            if ($row['check_in_time']) {
                $checkedIn++;
            }
            if ($row['visitor_card_id']) {
                $withCard++;
            }
        }

        $pending = $this->invitationModel
            ->whereIn('status', ['pending', 'submitted'])
            ->countAllResults();

        // Format data for the view
        $visitors = [];
        foreach ($results as $index => $row) {
            $cardStatusBadge = '-';
            if ($row['visitor_card_id']) {
                if ($row['card_status'] === 'in_use') {
                    $cardStatusBadge = 'In Use';
                } elseif ($row['card_status'] === 'active') {
                    $cardStatusBadge = 'Active';
                } else {
                    $cardStatusBadge = ucfirst($row['card_status']);
                }
            }

            $visitors[] = [
                'id' => $row['id'],
                'invitation_id' => $row['invitation_id'],
                'no' => $index + 1,
                'date' => date('d/m/Y', strtotime($row['created_at'])),
                'full_name' => $row['visitor_name'],
                'ic_passport' => $row['visitor_ic_passport'] ?? 'N/A',
                'contact' => $row['visitor_contact'] ?? 'N/A',
                'company' => $row['visitor_company'] ?? 'N/A',
                'host' => $row['host_name'] ?? 'N/A',
                'visit_purpose' => $row['visit_purpose'] ?? 'N/A',
                'reason' => $row['visit_purpose'] ?? 'N/A',
                'vehicle_reg' => $row['vehicle_reg'] ?? '',
                'location' => $row['location'] ?? 'N/A',
                'type' => $row['check_in_time'] ? 'Walk-in' : 'Pre-registered',
                'pass_no' => $row['card_epc'] ?? '',
                'card_epc' => $row['card_epc'] ?? null,
                'card_id' => $row['visitor_card_id'],
                'card_status' => $cardStatusBadge,
                'checked_in' => $row['check_in_time'] ? true : false,
                'check_in_time' => $row['check_in_time']
            ];
        }

        // Get available visitor cards for binding
        $availableCards = $this->visitorCardModel
            ->where('status', 'active')
            ->orderBy('card_id', 'ASC')
            ->findAll();

        $data = [
            'pageTitle' => 'Visitor List - SafeG',
            'stats' => [
                'total' => $totalApproved,
                'checkedIn' => $checkedIn,
                'withCard' => $withCard,
                'pending' => $pending
            ],
            'visitors' => $visitors,
            'availableCards' => $availableCards
        ];

        return view('visitors/list', $data);
    }

    /**
     * Bind visitor card to invitation visitor
     */
    public function bindCard()
    {
        $json = $this->request->getJSON();
        $invitationVisitorId = $json->invitation_visitor_id ?? null;
        $cardId = $json->card_id ?? null;
        $newCardEpc = $json->new_card_epc ?? null;

        if (!$invitationVisitorId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid visitor ID'
            ]);
        }

        // If new EPC is provided, create a new card
        if ($newCardEpc) {
            // Check if card with this EPC already exists
            $existingCard = $this->visitorCardModel->where('card_id', $newCardEpc)->first();
            
            if ($existingCard) {
                // Use existing card
                $cardId = $existingCard['id'];
                
                if ($existingCard['status'] !== 'active') {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'This card exists but is not available (status: ' . $existingCard['status'] . ')'
                    ]);
                }
            } else {
                // Create new card
                $newCardId = $this->visitorCardModel->insert([
                    'card_id' => $newCardEpc,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                if (!$newCardId) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to create new card'
                    ]);
                }
                
                $cardId = $newCardId;
            }
        }

        if (!$cardId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please select a card or enter a new EPC number'
            ]);
        }

        // Check if card is available
        $card = $this->visitorCardModel->find($cardId);
        if (!$card || $card['status'] !== 'active') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Card is not available'
            ]);
        }

        // Check if card is already assigned to another visitor today
        $db = \Config\Database::connect();
        $existingAssignment = $db->table('invitation_visitors iv')
            ->join('invitations i', 'i.id = iv.invitation_id')
            ->where('iv.visitor_card_id', $cardId)
            ->where('iv.id !=', $invitationVisitorId)
            ->where('iv.check_out_time IS NULL')
            ->where('DATE(i.created_at)', date('Y-m-d'))
            ->get()
            ->getRowArray();

        if ($existingAssignment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Card is currently assigned to another visitor'
            ]);
        }

        // Bind the card
        $updated = $this->invitationVisitorModel->update($invitationVisitorId, [
            'visitor_card_id' => $cardId
        ]);

        if (!$updated) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to bind card'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Card bound successfully',
            'card' => $card
        ]);
    }

    /**
     * Unbind visitor card from invitation visitor
     */
    public function unbindCard()
    {
        $invitationVisitorId = $this->request->getPost('invitation_visitor_id');

        if (!$invitationVisitorId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request parameter'
            ]);
        }

        // Get the current card assignment
        $invitationVisitor = $this->invitationVisitorModel->find($invitationVisitorId);
        if (!$invitationVisitor || !$invitationVisitor['visitor_card_id']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No card assigned to this visitor'
            ]);
        }

        // Unbind the card
        $updated = $this->invitationVisitorModel->update($invitationVisitorId, [
            'visitor_card_id' => null
        ]);

        if (!$updated) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to unbind card'
            ]);
        }

        // Reset card status to active if it was in use
        $this->visitorCardModel->update($invitationVisitor['visitor_card_id'], [
            'status' => 'active'
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Card unbound successfully'
        ]);
    }
}
