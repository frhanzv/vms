<?php

namespace App\Controllers;

// use App\Models\StaffModel;          // To be defined when table is known
// use App\Models\StaffCardModel;      // To be defined when card table is known

class StaffList extends BaseController
{
    // protected $staffModel;
    // protected $staffCardModel;

    public function __construct()
    {
        // $this->staffModel = new StaffModel();
        // $this->staffCardModel = new StaffCardModel();
    }

    public function index()
    {
        // Get all staff with card details
        // $db = \Config\Database::connect();
        // $builder = $db->table('staff_table s');       // Replace 'staff_table' with actual table name
        // $builder->select('s.*,
        //                   sc.card_id as card_epc,
        //                   sc.status as card_status');
        // $builder->join('staff_cards sc', 'sc.id = s.staff_card_id', 'left');   // Replace with actual card table
        // $builder->orderBy('s.created_at', 'DESC');

        // $results = $builder->get()->getResultArray();

        // Count statistics
        // $totalStaff = count($results);
        // $checkedIn = 0;
        // $withCard = 0;

        // foreach ($results as $row) {
        //     if ($row['check_in_time']) {
        //         $checkedIn++;
        //     }
        //     if ($row['staff_card_id']) {
        //         $withCard++;
        //     }
        // }

        // Format data for the view
        // $staffList = [];
        // foreach ($results as $index => $row) {
        //     $cardStatusBadge = '-';
        //     if ($row['staff_card_id']) {
        //         if ($row['card_status'] === 'in_use') {
        //             $cardStatusBadge = 'In Use';
        //         } elseif ($row['card_status'] === 'active') {
        //             $cardStatusBadge = 'Active';
        //         } else {
        //             $cardStatusBadge = ucfirst($row['card_status']);
        //         }
        //     }

        //     $staffList[] = [
        //         'id'          => $row['id'],
        //         'no'          => $index + 1,
        //         'date'        => date('d/m/Y', strtotime($row['created_at'])),
        //         'full_name'   => $row['full_name'] ?? 'N/A',
        //         'ic_passport' => $row['ic_passport'] ?? 'N/A',
        //         'contact'     => $row['contact'] ?? 'N/A',
        //         'company'     => $row['company'] ?? 'N/A',
        //         'department'  => $row['department'] ?? 'N/A',
        //         'card_epc'    => $row['card_epc'] ?? null,
        //         'card_id'     => $row['staff_card_id'] ?? null,
        //         'card_status' => $cardStatusBadge,
        //         'checked_in'  => $row['check_in_time'] ? true : false,
        //         'check_in_time' => $row['check_in_time']
        //     ];
        // }

        // Get available cards for binding
        // $availableCards = $this->staffCardModel
        //     ->where('status', 'active')
        //     ->orderBy('card_id', 'ASC')
        //     ->findAll();

        $data = [
            'pageTitle' => 'Staff List - SafeG',
            // 'stats' => [
            //     'total'     => $totalStaff,
            //     'checkedIn' => $checkedIn,
            //     'withCard'  => $withCard,
            // ],
            // 'staffList'      => $staffList,
            // 'availableCards' => $availableCards
        ];

        return view('staffs/list', $data);
    }

    /**
     * Bind card to staff member
     */
    // public function bindCard()
    // {
    //     $json = $this->request->getJSON();
    //     $staffId    = $json->staff_id ?? null;
    //     $cardId     = $json->card_id ?? null;
    //     $newCardEpc = $json->new_card_epc ?? null;

    //     if (!$staffId) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Invalid staff ID'
    //         ]);
    //     }

    //     if ($newCardEpc) {
    //         $existingCard = $this->staffCardModel->where('card_id', $newCardEpc)->first();

    //         if ($existingCard) {
    //             $cardId = $existingCard['id'];

    //             if ($existingCard['status'] !== 'active') {
    //                 return $this->response->setJSON([
    //                     'success' => false,
    //                     'message' => 'This card exists but is not available (status: ' . $existingCard['status'] . ')'
    //                 ]);
    //             }
    //         } else {
    //             $newCardId = $this->staffCardModel->insert([
    //                 'card_id'    => $newCardEpc,
    //                 'status'     => 'active',
    //                 'created_at' => date('Y-m-d H:i:s'),
    //                 'updated_at' => date('Y-m-d H:i:s')
    //             ]);

    //             if (!$newCardId) {
    //                 return $this->response->setJSON([
    //                     'success' => false,
    //                     'message' => 'Failed to create new card'
    //                 ]);
    //             }

    //             $cardId = $newCardId;
    //         }
    //     }

    //     if (!$cardId) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Please select a card or enter a new EPC number'
    //         ]);
    //     }

    //     $card = $this->staffCardModel->find($cardId);
    //     if (!$card || $card['status'] !== 'active') {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Card is not available'
    //         ]);
    //     }

    //     $updated = $this->staffModel->update($staffId, [
    //         'staff_card_id' => $cardId
    //     ]);

    //     if (!$updated) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Failed to bind card'
    //         ]);
    //     }

    //     return $this->response->setJSON([
    //         'success' => true,
    //         'message' => 'Card bound successfully',
    //         'card'    => $card
    //     ]);
    // }

    /**
     * Unbind card from staff member
     */
    // public function unbindCard()
    // {
    //     $staffId = $this->request->getPost('staff_id');

    //     if (!$staffId) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Invalid request parameter'
    //         ]);
    //     }

    //     $staff = $this->staffModel->find($staffId);
    //     if (!$staff || !$staff['staff_card_id']) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'No card assigned to this staff member'
    //         ]);
    //     }

    //     $updated = $this->staffModel->update($staffId, [
    //         'staff_card_id' => null
    //     ]);

    //     if (!$updated) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Failed to unbind card'
    //         ]);
    //     }

    //     $this->staffCardModel->update($staff['staff_card_id'], [
    //         'status' => 'active'
    //     ]);

    //     return $this->response->setJSON([
    //         'success' => true,
    //         'message' => 'Card unbound successfully'
    //     ]);
    // }
}