<?php
$content = file_get_contents('app/Controllers/Config.php');
$content = preg_replace('/}\s*$/', "
    // ============== DEVICE ASSIGNMENTS METHODS ==============

    public function getDeviceAssignments()
    {
        \$page = \$this->request->getGet('page') ?? 1;
        \$perPage = \$this->request->getGet('per_page') ?? 10;
        \$search = \$this->request->getGet('search') ?? '';
        \$offset = (\$page - 1) * \$perPage;

        \$devices = \$this->deviceAssignmentModel->getDeviceAssignmentsWithPagination(\$search, \$perPage, \$offset);
        \$total = \$this->deviceAssignmentModel->getTotalDeviceAssignments(\$search);

        return \$this->response->setJSON([
            'success' => true,
            'data' => \$devices,
            'pagination' => [
                'current_page' => (int)\$page,
                'per_page' => (int)\$perPage,
                'total' => \$total,
                'total_pages' => ceil(\$total / \$perPage),
                'from' => \$offset + 1,
                'to' => min(\$offset + \$perPage, \$total)
            ]
        ]);
    }

    public function getDeviceAssignment(\$id)
    {
        \$device = \$this->deviceAssignmentModel->find(\$id);
        if (!\$device) {
            return \$this->response->setJSON(['success' => false, 'message' => 'Device not found'])->setStatusCode(404);
        }
        return \$this->response->setJSON(['success' => true, 'data' => \$device]);
    }

    public function createDeviceAssignment()
    {
        \$input = \$this->request->getJSON(true);
        \$rules = [
            'device_id' => 'required|max_length[50]',
            'ip_address' => 'required|valid_ip',
            'status' => 'required|in_list[Online,Offline]',
            'registration_status' => 'required|in_list[Registered,Unregistered]',
            'location_id' => 'required|numeric',
            'type' => 'required|in_list[Check-In,Check-Out]',
            'last_heartbeat' => 'permit_empty|valid_date[Y-m-d H:i:s]'
        ];

        if (!\$this->validate(\$rules)) {
            return \$this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => \$this->validator->getErrors()
            ])->setStatusCode(400);
        }

        try {
            if (\$this->deviceAssignmentModel->insert(\$input)) {
                return \$this->response->setJSON(['success' => true, 'message' => 'Device Assignment created successfully']);
            }
            return \$this->response->setJSON(['success' => false, 'message' => 'Failed to create Device Assignment'])->setStatusCode(500);
        } catch (\Exception \$e) {
            return \$this->response->setJSON(['success' => false, 'message' => \$e->getMessage()])->setStatusCode(500);
        }
    }

    public function updateDeviceAssignment(\$id)
    {
        if (!\$this->deviceAssignmentModel->find(\$id)) {
            return \$this->response->setJSON(['success' => false, 'message' => 'Device not found'])->setStatusCode(404);
        }

        \$input = \$this->request->getJSON(true);
        \$rules = [
            'device_id' => 'required|max_length[50]',
            'ip_address' => 'required|valid_ip',
            'status' => 'required|in_list[Online,Offline]',
            'registration_status' => 'required|in_list[Registered,Unregistered]',
            'location_id' => 'required|numeric',
            'type' => 'required|in_list[Check-In,Check-Out]',
            'last_heartbeat' => 'permit_empty|valid_date[Y-m-d H:i:s]'
        ];

        if (!\$this->validate(\$rules)) {
            return \$this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => \$this->validator->getErrors()
            ])->setStatusCode(400);
        }

        if (\$this->deviceAssignmentModel->update(\$id, \$input)) {
            return \$this->response->setJSON(['success' => true, 'message' => 'Device Assignment updated successfully']);
        }
        return \$this->response->setJSON(['success' => false, 'message' => 'Failed to update Device Assignment'])->setStatusCode(500);
    }

    public function deleteDeviceAssignment(\$id)
    {
        if (!\$this->deviceAssignmentModel->find(\$id)) {
            return \$this->response->setJSON(['success' => false, 'message' => 'Device not found'])->setStatusCode(404);
        }
        if (\$this->deviceAssignmentModel->delete(\$id)) {
            return \$this->response->setJSON(['success' => true, 'message' => 'Device Assignment deleted successfully']);
        }
        return \$this->response->setJSON(['success' => false, 'message' => 'Failed to delete Device Assignment'])->setStatusCode(500);
    }

    // ============== SETTINGS METHODS ==============

    public function getIpRangeSettings()
    {
        return \$this->response->setJSON([
            'success' => true,
            'data' => [
                'ip_range_from' => \$this->settingModel->getSetting('ip_range_from'),
                'ip_range_to' => \$this->settingModel->getSetting('ip_range_to')
            ]
        ]);
    }

    public function saveIpRangeSettings()
    {
        \$input = \$this->request->getJSON(true);
        \$rules = [
            'ip_range_from' => 'required|valid_ip',
            'ip_range_to' => 'required|valid_ip'
        ];

        if (!\$this->validate(\$rules)) {
            return \$this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => \$this->validator->getErrors()
            ])->setStatusCode(400);
        }

        \$this->settingModel->setSetting('ip_range_from', \$input['ip_range_from']);
        \$this->settingModel->setSetting('ip_range_to', \$input['ip_range_to']);

        return \$this->response->setJSON([
            'success' => true,
            'message' => 'IP Range settings saved successfully'
        ]);
    }
}
", $content);
file_put_contents('app/Controllers/Config.php', $content);
echo "Appended";
