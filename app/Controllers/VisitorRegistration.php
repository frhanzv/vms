<?php

namespace App\Controllers;

use App\Models\InvitationModel;
use App\Models\InvitationScheduleModel;
use App\Models\LocationModel;
use App\Models\CountryModel;
use App\Models\StateModel;
use App\Models\CityModel;
use App\Models\CompanyModel;
use App\Models\VisitorLicenseModel;
use App\Models\VisitorEquipmentModel;

class VisitorRegistration extends BaseController
{
    protected $invitationModel;
    protected $scheduleModel;
    protected $locationModel;
    protected $countryModel;
    protected $stateModel;
    protected $cityModel;
    protected $companyModel;
    protected $licenseModel;
    protected $equipmentModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->invitationModel = new InvitationModel();
        $this->scheduleModel = new InvitationScheduleModel();
        $this->locationModel = new LocationModel();
        $this->countryModel = new CountryModel();
        $this->stateModel = new StateModel();
        $this->cityModel = new CityModel();
        $this->companyModel = new CompanyModel();
        $this->licenseModel = new VisitorLicenseModel();
        $this->equipmentModel = new VisitorEquipmentModel();
    }

    public function index()
    {
        // Get invitation token from URL
        $token = $this->request->getGet('token');
        $invitationId = null;
        $invitation = null;
        $selectedLocations = [];
        $schedules = [];
        
        if ($token) {
            $invitationId = base64_decode($token);
            $invitation = $this->invitationModel->find($invitationId);
            
            // Load invitation schedules
            if ($invitation) {
                $schedules = $this->scheduleModel->where('invitation_id', $invitationId)->findAll();
            }
            
            // Parse selected locations from invitation (comma-separated string)
            if ($invitation && !empty($invitation['location'])) {
                $selectedLocations = array_map('trim', explode(',', $invitation['location']));
            }
        }
        
        // Get active locations from database
        $locations = $this->locationModel->where('status', 'active')->findAll();
        
        // Get active countries, states, cities from database
        $countries = $this->countryModel->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        $states = $this->stateModel->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        $cities = $this->cityModel->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        
        // Get company registration ID if company name exists in invitation
        $companyRegistrationId = null;
        if ($invitation && !empty($invitation['company'])) {
            $company = $this->companyModel->where('name', $invitation['company'])->first();
            if ($company) {
                $companyRegistrationId = $company['registration_no'];
            }
        }
        
        $data = [
            'pageTitle' => 'Visitor Registration - SafeG',
            'token' => $token,
            'invitationId' => $invitationId,
            'invitation' => $invitation,
            'schedules' => $schedules,
            'locations' => $locations,
            'selectedLocations' => $selectedLocations,
            'countries' => $countries,
            'states' => $states,
            'cities' => $cities,
            'companyRegistrationId' => $companyRegistrationId
        ];

        return view('visitors/registration', $data);
    }

    public function submit()
    {
        // Handle form submission
        $validationRules = [
            'full_name' => 'required|max_length[255]',
            'ic_number' => 'required|max_length[50]',
            'contact_number' => 'required|max_length[20]',
            'email' => 'required|valid_email',
            'date_of_birth' => 'required',
            'sex' => 'required|in_list[MALE,FEMALE]',
            'resident' => 'required|in_list[LOCAL,FOREIGN]',
            'company_visiting' => 'required'
        ];

        if (!$this->validate($validationRules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Check if this is an update (invitation exists from token)
            $token = $this->request->getPost('token');
            $invitationId = null;
            
            if ($token) {
                $invitationId = base64_decode($token);
            }

            // Handle file uploads
            $uploadPath = WRITEPATH . 'uploads/visitors/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $governmentIdPath = null;
            $invitationLetterPath = null;
            $profilePhotoPath = null;

            // Upload Government ID
            $governmentId = $this->request->getFile('government_id');
            if ($governmentId && $governmentId->isValid() && !$governmentId->hasMoved()) {
                $newName = 'gov_id_' . time() . '_' . $governmentId->getRandomName();
                $governmentId->move($uploadPath, $newName);
                $governmentIdPath = 'visitors/' . $newName;
            }

            // Upload Invitation Letter
            $invitationLetter = $this->request->getFile('invitation_letter');
            if ($invitationLetter && $invitationLetter->isValid() && !$invitationLetter->hasMoved()) {
                $newName = 'invitation_' . time() . '_' . $invitationLetter->getRandomName();
                $invitationLetter->move($uploadPath, $newName);
                $invitationLetterPath = 'visitors/' . $newName;
            }

            // Upload Profile Photo
            $profilePhoto = $this->request->getFile('profile_photo');
            if ($profilePhoto && $profilePhoto->isValid() && !$profilePhoto->hasMoved()) {
                $newName = 'profile_' . time() . '_' . $profilePhoto->getRandomName();
                $profilePhoto->move($uploadPath, $newName);
                $profilePhotoPath = 'visitors/' . $newName;
            }

            // Get company visiting (could be multiple)
            $companyVisiting = $this->request->getPost('company_visiting');
            $companyVisitingStr = is_array($companyVisiting) ? implode(', ', $companyVisiting) : $companyVisiting;

            // Combine address fields
            $addressParts = array_filter([
                $this->request->getPost('address_1'),
                $this->request->getPost('address_2'),
                $this->request->getPost('address_3')
            ]);
            $fullAddress = implode(', ', $addressParts);

            // Prepare visitor data - save ALL fields
            $visitorData = [
                'full_name' => $this->request->getPost('full_name'),
                'ic_passport' => $this->request->getPost('ic_number'),
                'contact' => $this->request->getPost('contact_number'),
                'visitor_email' => $this->request->getPost('email'),
                'date_of_birth' => $this->request->getPost('date_of_birth'),
                'sex' => $this->request->getPost('sex'),
                'resident' => $this->request->getPost('resident'),
                'address' => $fullAddress,
                'postcode' => $this->request->getPost('postal_code') ?? '',
                'city' => $this->request->getPost('city') ?? '',
                'state' => $this->request->getPost('state') ?? '',
                'country' => $this->request->getPost('country') ?? '',
                'company' => $this->request->getPost('company_name') ?? '',
                'registration_no' => $this->request->getPost('company_reg_id') ?? '',
                'vehicle_registration' => $this->request->getPost('vehicle_registration') ?? '',
                'vehicle_category' => $this->request->getPost('category') ?? '',
                'vehicle_type' => $this->request->getPost('vehicle_type') ?? '',
                'staff_id' => $this->request->getPost('staff_id') ?? '',
                'host_contact' => $this->request->getPost('host_contact') ?? '',
                'company_visited' => $this->request->getPost('company_visited') ?? '',
                'location' => $companyVisitingStr,
                'reason' => $this->request->getPost('visit_reason') ?? '',
                'status' => 'Submitted',
                'government_id_path' => $governmentIdPath,
                'invitation_letter_path' => $invitationLetterPath,
                'profile_photo_path' => $profilePhotoPath
            ];

            // Insert or update invitation record
            if ($invitationId && ($existingInvitation = $this->invitationModel->find($invitationId))) {
                // Prevent double-submission: only allow if status is Pending or Submitted
                if (!in_array($existingInvitation['status'], ['Pending', 'Submitted'], true)) {
                    throw new \Exception('This registration has already been processed (status: ' . $existingInvitation['status'] . '). It cannot be modified.');
                }

                // Atomic status-guarded update
                $visitorData['version'] = ($existingInvitation['version'] ?? 1) + 1;
                $db->table('invitations')
                    ->where('id', $invitationId)
                    ->whereIn('status', ['Pending', 'Submitted'])
                    ->update($visitorData);

                if ($db->affectedRows() === 0) {
                    throw new \Exception('This registration has been modified by someone else. Please refresh and try again.');
                }
                
                // Delete old schedules before inserting new ones
                $this->scheduleModel->where('invitation_id', $invitationId)->delete();
            } else {
                // Insert new invitation
                $invitationId = $this->invitationModel->insert($visitorData);
                if (!$invitationId) {
                    $errors = $this->invitationModel->errors();
                    throw new \Exception('Failed to create invitation record: ' . ($errors ? json_encode($errors) : 'Unknown error'));
                }
            }

            // Save visit schedules
            $dates = $this->request->getPost('dates');
            if (is_array($dates) && count($dates) > 0) {
                foreach ($dates as $dateEntry) {
                    if (isset($dateEntry['date_from']) && isset($dateEntry['date_to'])) {
                        $scheduleData = [
                            'invitation_id' => $invitationId,
                            'date_from' => $dateEntry['date_from'],
                            'date_to' => $dateEntry['date_to']
                        ];
                        $this->scheduleModel->insert($scheduleData);
                    }
                }
            }

            // Save driving licenses
            if ($invitationId && $this->invitationModel->find($invitationId)) {
                // Delete old licenses if updating
                $this->licenseModel->where('invitation_id', $invitationId)->delete();
            }
            
            $licenses = $this->request->getPost('licenses');
            if (is_array($licenses) && count($licenses) > 0) {
                foreach ($licenses as $license) {
                    if (!empty($license['class'])) {
                        $licenseData = [
                            'invitation_id' => $invitationId,
                            'license_class' => $license['class'] ?? '',
                            'expiry_date' => $license['expiry'] ?? null
                        ];
                        $this->licenseModel->insert($licenseData);
                    }
                }
            }

            // Save equipment/assets
            if ($invitationId && $this->invitationModel->find($invitationId)) {
                // Delete old equipment if updating
                $this->equipmentModel->where('invitation_id', $invitationId)->delete();
            }
            
            $equipment = $this->request->getPost('equipment');
            if (is_array($equipment) && count($equipment) > 0) {
                foreach ($equipment as $item) {
                    if (!empty($item['category']) || !empty($item['type'])) {
                        $equipmentData = [
                            'invitation_id' => $invitationId,
                            'category' => $item['category'] ?? '',
                            'equipment_type' => $item['type'] ?? '',
                            'size' => $item['size'] ?? '',
                            'transport' => $item['transport'] ?? '',
                            'purpose' => $item['purpose'] ?? '',
                            'voltage' => $item['voltage'] ?? '',
                            'quantity' => $item['quantity'] ?? 1,
                            'serial_number' => $item['serial'] ?? ''
                        ];
                        $this->equipmentModel->insert($equipmentData);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to save registration data'
                ]);
            }

            // Generate token for next step
            $token = base64_encode($invitationId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Registration completed successfully',
                'redirect' => base_url('security/briefing?token=' . $token)
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Registration submission error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function processMyKad()
    {
        // Check if file was uploaded
        $file = $this->request->getFile('mykad_image');
        
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No valid image uploaded'
            ]);
        }

        try {
            // Move uploaded file to temp location
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/temp', $newName);
            $imagePath = WRITEPATH . 'uploads/temp/' . $newName;

            // Process image with OCR
            $extractedData = $this->extractMyKadData($imagePath);

            // Delete temp file
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            if ($extractedData) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $extractedData,
                    'message' => 'Data extracted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Could not extract data from image'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'MyKad OCR error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error processing image: ' . $e->getMessage()
            ]);
        }
    }

    private function extractMyKadData($imagePath)
    {
        try {
            // Use Google Cloud Vision OCR Python script for better accuracy
            $pythonPath = str_replace('/', DIRECTORY_SEPARATOR, ROOTPATH . '.venv/Scripts/python.exe');
            $scriptPath = str_replace('/', DIRECTORY_SEPARATOR, ROOTPATH . 'ocr_mykad.py');
            $credentialsPath = str_replace('/', DIRECTORY_SEPARATOR, ROOTPATH . 'credentials/vms-mykad-ocr-13799932dbd4.json');
            
            // Check if Python and script exist
            if (!file_exists($pythonPath)) {
                log_message('error', 'Python not found at: ' . $pythonPath);
                throw new \Exception('Python environment not configured');
            }
            
            if (!file_exists($scriptPath)) {
                log_message('error', 'OCR script not found at: ' . $scriptPath);
                throw new \Exception('OCR script not found');
            }
            
            if (!file_exists($credentialsPath)) {
                log_message('error', 'Google Cloud credentials not found at: ' . $credentialsPath);
                throw new \Exception('Google Cloud credentials not configured');
            }
            
            // Execute Python Google Cloud Vision OCR script with credentials
            // Set environment variable for Google Cloud authentication
            // NOTE: Do NOT quote the credentials path in SET command, only quote the python paths
            $command = 'set GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath . ' && "' . $pythonPath . '" "' . $scriptPath . '" "' . $imagePath . '" 2>nul';
            
            log_message('info', 'Executing Google Cloud Vision OCR: ' . $command);
            
            $output = shell_exec($command);
            
            // Find JSON in output
            $jsonStart = strpos($output, '{');
            $jsonEnd = strrpos($output, '}');
            
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonStr = substr($output, $jsonStart, $jsonEnd - $jsonStart + 1);
                $result = json_decode($jsonStr, true);
            } else {
                $result = null;
            }
            
            log_message('info', 'Google Cloud Vision OCR Parsed Result: ' . json_encode($result));
            
            if (!$result || !isset($result['success'])) {
                log_message('error', 'Invalid OCR response: ' . $output);
                throw new \Exception('Failed to parse OCR output');
            }
            
            if (!$result['success']) {
                log_message('error', 'OCR error: ' . ($result['error'] ?? 'Unknown error'));
                throw new \Exception('OCR failed: ' . ($result['error'] ?? 'Unknown error'));
            }
            
            $text = $result['text'] ?? '';
            
            log_message('info', 'OCR Text: ' . json_encode($text));
            log_message('info', 'OCR Confidence: ' . ($result['confidence'] ?? 'unknown'));
            log_message('info', 'OCR Avg Confidence: ' . ($result['avg_confidence'] ?? 'N/A'));
            log_message('info', 'OCR Text Lines: ' . json_encode($result['lines'] ?? []));
            
            $data = $this->parseMyKadText($text);
            
            // Log what was extracted
            log_message('info', 'Extracted Data: ' . json_encode($data));
            
            // Add warning if IC number not detected
            if (!$data['ic_number']) {
                $data['warning'] = 'IC number not detected from image. This usually happens when the IC number is not clearly visible due to lighting, glare, or image quality. Please enter it manually.';
                log_message('warning', 'IC number not detected from MyKad image');
            }
            
            // Return OCR text for debugging
            $data['raw_ocr_text'] = $text;
            $data['ocr_quality'] = $result['confidence'] ?? 'medium';
            
            return $data;
        } catch (\Exception $e) {
            log_message('error', 'EasyOCR processing failed: ' . $e->getMessage());
            
            // Return empty structure instead of null
            return [
                'ic_number' => null,
                'name' => null,
                'date_of_birth' => null,
                'sex' => null,
                'address' => null,
                'postcode' => null,
                'city' => null,
                'state' => null,
                'ocr_quality' => 'failed'
            ];
        }
    }

    private function preprocessImage($imagePath)
    {
        // Check if GD library is available
        if (!extension_loaded('gd')) {
            return $imagePath;
        }

        try {
            // Load image
            $imageType = exif_imagetype($imagePath);
            
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($imagePath);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($imagePath);
                    break;
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($imagePath);
                    break;
                default:
                    return $imagePath;
            }

            if (!$image) {
                return $imagePath;
            }

            // Get image dimensions
            $width = imagesx($image);
            $height = imagesy($image);

            // Simple preprocessing - just grayscale and high contrast
            imagefilter($image, IMG_FILTER_GRAYSCALE);
            imagefilter($image, IMG_FILTER_CONTRAST, -60);
            imagefilter($image, IMG_FILTER_BRIGHTNESS, 20);

            // Save processed image
            $processedPath = WRITEPATH . 'uploads/temp/processed_' . basename($imagePath);
            imagejpeg($image, $processedPath, 100);
            
            imagedestroy($image);

            return $processedPath;
        } catch (\Exception $e) {
            log_message('error', 'Image preprocessing failed: ' . $e->getMessage());
            return $imagePath;
        }
    }

    private function parseMyKadText($text)
    {
        // Parse OCR text to extract MyKad information
        $data = [
            'ic_number' => null,
            'name' => null,
            'date_of_birth' => null,
            'sex' => null,
            'address' => null,
            'postcode' => null,
            'city' => null,
            'state' => null
        ];

        // Keep original text for line-based parsing (preserve line breaks!)
        $originalText = $text;
        
        // Clean special characters from text but KEEP line structure
        $originalText = preg_replace('/[„"*☐□■▪●○◦•]/u', '', $originalText);
        $originalText = preg_replace('/\.{2,}/', '', $originalText); // Remove multiple dots
        $originalText = trim($originalText);
        
        // Remove extra whitespace and clean text
        $cleanText = $this->cleanOcrDigits($text);
        
        // Extract IC number - be VERY flexible with OCR errors
        // Pattern: 6 digits/chars, separator, 2 digits/chars, separator, 4 digits/chars
        // OCR often confuses: 0/O, 1/l/I, 5/S, 8/B, 9/g, etc.
        $icPatterns = [
            '/\b([0-9OogIl]{6})[\s\-\.]([0-9OogIlf]{2})[\s\-\.]([0-9OogIlSsB]{4})\b/',
            '/([0-9OogIl]{4,7})[\s\-]([0-9OogIlf]{1,3})[\s\-]([0-9OogIlSsB]{4,6})/',
            '/(\d{6})[\s\-](\d{2})[\s\-](\d{4})/',
        ];
        
        foreach ($icPatterns as $pattern) {
            if (preg_match($pattern, $cleanText, $matches)) {
                // Clean up OCR errors
                $ic1 = $this->cleanOcrDigits($matches[1]);
                $ic2 = $this->cleanOcrDigits($matches[2]);
                $ic3 = $this->cleanOcrDigits($matches[3]);
                
                log_message('info', 'IC Pattern matched: ' . $matches[0] . ' -> Cleaned: ' . $ic1 . '-' . $ic2 . '-' . $ic3);
                
                // Flexible padding - take LAST N digits for each part
                if (strlen($ic1) > 6) {
                    $ic1 = substr($ic1, -6); // Take last 6
                } else {
                    $ic1 = str_pad($ic1, 6, '0', STR_PAD_LEFT); // Pad to 6
                }
                
                if (strlen($ic2) > 2) {
                    $ic2 = substr($ic2, -2); // Take last 2
                } else {
                    $ic2 = str_pad($ic2, 2, '0', STR_PAD_LEFT); // Pad to 2
                }
                
                if (strlen($ic3) > 4) {
                    $ic3 = substr($ic3, -4); // Take last 4
                } else {
                    $ic3 = str_pad($ic3, 4, '0', STR_PAD_LEFT); // Pad to 4
                }
                
                log_message('info', 'IC after padding: ' . $ic1 . '-' . $ic2 . '-' . $ic3);
                
                // Basic validation - must be all digits after cleaning
                if (ctype_digit($ic1 . $ic2 . $ic3)) {
                    $data['ic_number'] = $ic1 . $ic2 . $ic3;
                    
                    // Extract date of birth from IC number (YYMMDD format)
                    $year = substr($ic1, 0, 2);
                    $month = substr($ic1, 2, 2);
                    $day = substr($ic1, 4, 2);
                    
                    // Validate date parts
                    if ($month >= 1 && $month <= 12 && $day >= 1 && $day <= 31) {
                        $fullYear = (intval($year) > 50) ? '19' . $year : '20' . $year;
                        $data['date_of_birth'] = $fullYear . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                        
                        // Determine sex from IC (odd last digit = male, even = female)
                        $lastDigit = intval(substr($data['ic_number'], -1));
                        $data['sex'] = ($lastDigit % 2 == 0) ? 'FEMALE' : 'MALE';
                    }
                    break;
                }
            }
        }

        // Extract sex from LELAKI/PEREMPUAN text on card
        if (preg_match('/LELAKI/i', $originalText)) {
            $data['sex'] = 'MALE';
        } elseif (preg_match('/PEREMPUAN/i', $originalText)) {
            $data['sex'] = 'FEMALE';
        }
        
        // Extract resident status from WARGANEGARA text
        if (preg_match('/WARGANEGARA/i', $originalText)) {
            // If WARGANEGARA is found, it's a local Malaysian
            // Note: This could be added to form if needed
        }

        // Extract name - handle various MyKad formats
        $lines = explode("\n", $originalText);
        $nameParts = [];
        $foundBin = false;
        
        // First pass: look for complete name patterns
        foreach ($lines as $i => $line) {
            $line = trim($line);
            
            // Skip empty lines
            if (empty($line) || strlen($line) < 3) continue;
            
            // Skip IC number, postcode, religion, sex, state, citizenship, card type, card header
            if (preg_match('/\d{6}[\-\s]\d{2}[\-\s]\d{4}|^(ISLAM|LELAKI|PEREMPUAN|WARGANEGARA|KELANTAN|JOHOR|MALA|MKAD|LELANG|MA|DEN|IDENTITY|CARD|KOLAM)$/i', $line)) continue;
            
            // Clean special characters from line
            $cleanLine = preg_replace('/[^A-Z\s\/]/i', '', $line);
            $cleanLine = trim($cleanLine);
            
            if (empty($cleanLine) || strlen($cleanLine) < 3) continue;
            
            // Pattern: "FIRSTNAME BIN/BINTI LASTNAME" in one line
            if (preg_match('/^([A-Z\s]+?)\s+(BIN|BINTI)\s+([A-Z\s]+?)$/i', $cleanLine, $matches)) {
                $data['name'] = strtoupper(trim($matches[1] . ' ' . $matches[2] . ' ' . $matches[3]));
                $foundBin = true;
                log_message('info', 'Name found in one line: ' . $data['name']);
                break;
            }
            
            // Collect name parts if contains BIN/BINTI
            if (preg_match('/\b(BIN|BINTI)\b/i', $cleanLine)) {
                // Check ALL previous lines for potential first name (within 3 lines)
                if ($i > 0 && !$foundBin) {
                    for ($j = $i - 1; $j >= max(0, $i - 3); $j--) {
                        $prevLine = trim($lines[$j]);
                        // Skip IC number line
                        if (preg_match('/\d{6}[\-\s]\d{2}[\-\s]\d{4}/', $prevLine)) continue;
                        
                        $prevLine = preg_replace('/[^A-Z\s]/i', '', $prevLine);
                        $prevLine = trim($prevLine);
                        
                        // Must be 4+ chars, not keywords
                        if (strlen($prevLine) >= 4 && 
                            !preg_match('/^(KAMPUNG|JALAN|ISLAM|LELAKI|PEREMPUAN|WARGANEGARA|MA|DEN|IDENTITY|CARD|MKAD|MALA|KOLAM|KAD)$/i', $prevLine)) {
                            // Add to front of name parts
                            array_unshift($nameParts, $prevLine);
                            log_message('info', 'Found name part from previous line: ' . $prevLine);
                        }
                    }
                }
                $nameParts[] = $cleanLine;
                $data['name'] = strtoupper(implode(' ', $nameParts));
                $foundBin = true;
                log_message('info', 'Name assembled from parts: ' . $data['name']);
                break;
            }
        }
        
        // Second pass: look for split name across lines
        if (!$foundBin) {
            foreach ($lines as $i => $line) {
                $line = trim($line);
                $cleanLine = preg_replace('/[^A-Z\s]/i', '', $line);
                $cleanLine = trim($cleanLine);
                
                // Found BIN/BINTI on its own line OR as start of line "BIN WAN KAR MIZI"
                if (preg_match('/^(BIN|BINTI)\s+(.+)$/i', $cleanLine, $matches)) {
                    $foundBin = true;
                    
                    // Look for first name in previous line
                    if ($i > 0) {
                        $prevLine = trim($lines[$i-1]);
                        $prevLine = preg_replace('/[^A-Z\s]/i', '', $prevLine);
                        $prevLine = trim($prevLine);
                        // Must be 3+ chars, no digits, not keywords
                        if (strlen($prevLine) >= 3 && 
                            !preg_match('/KAMPUNG|JALAN|ISLAM|LELAKI|PEREMPUAN|WARGANEGARA|KELANTAN|JOHOR|MALA|MKAD|MA|DEN/i', $prevLine)) {
                            $nameParts[] = $prevLine;
                        }
                    }
                    
                    // Add "BIN/BINTI LASTNAME"
                    $nameParts[] = $cleanLine;
                    break;
                }
                
                // Found standalone BIN/BINTI
                if (preg_match('/^(BIN|BINTI)$/i', $cleanLine)) {
                    $foundBin = true;
                    
                    // Look for first name in previous 1-2 lines
                    for ($j = $i - 1; $j >= max(0, $i - 2); $j--) {
                        $prevLine = trim($lines[$j]);
                        $prevLine = preg_replace('/[^A-Z\s]/i', '', $prevLine);
                        $prevLine = trim($prevLine);
                        if (strlen($prevLine) >= 3 && 
                            !preg_match('/KAMPUNG|JALAN|ISLAM|LELAKI|PEREMPUAN|WARGANEGARA|KELANTAN|JOHOR|MALA|MKAD|MA|DEN/i', $prevLine)) {
                            array_unshift($nameParts, $prevLine);
                        }
                    }
                    
                    $nameParts[] = $cleanLine; // Add BIN/BINTI
                    
                    // Look for last name in next 1-2 lines
                    for ($j = $i + 1; $j <= min(count($lines) - 1, $i + 2); $j++) {
                        $nextLine = trim($lines[$j]);
                        $nextLine = preg_replace('/[^A-Z\s]/i', '', $nextLine);
                        $nextLine = trim($nextLine);
                        if (strlen($nextLine) >= 3 && 
                            !preg_match('/\d{5}|KAMPUNG|JALAN|ISLAM|LELAKI|PEREMPUAN|WARGANEGARA|KELANTAN|JOHOR|MALA|MKAD/i', $nextLine)) {
                            $nameParts[] = $nextLine;
                            break;
                        }
                    }
                    break;
                }
            }
            
            if (!empty($nameParts)) {
                $data['name'] = strtoupper(implode(' ', $nameParts));
            }
        }

        // Extract postcode and city (format: "$ 3019 PONTIAN" or "82010 PONTIAN" or "18500 MACHANG")
        // Google Cloud Vision may add $ or other symbols before postcodes
        // IMPORTANT: Exclude IC number pattern (6-2-4 digits)
        $lines_for_postcode = explode("\n", $originalText);
        foreach ($lines_for_postcode as $line) {
            $line = trim($line);
            // Skip IC number lines
            if (preg_match('/\d{6}[\-\s]\d{2}[\-\s]\d{4}/', $line)) continue;
            
            // Look for postcode + city pattern (5 digits followed by city name)
            if (preg_match('/[\$\s]*(\d{5})\s+([A-Z]+)/i', $line, $matches)) {
                $postcode = $matches[1];
                $city = strtoupper(trim($matches[2]));
                
                // Basic validation: Malaysian postcodes should start with valid state codes
                // First 2 digits indicate region/state
                $firstTwo = substr($postcode, 0, 2);
                if ($firstTwo >= '01' && $firstTwo <= '99') {
                    $data['postcode'] = $postcode;
                    $data['city'] = $city;
                    log_message('info', 'Postcode and city extracted: ' . $postcode . ' ' . $city);
                    break;
                }
            }
            // Look for 4-digit postcode
            if (!$data['postcode'] && preg_match('/[\$\s]*(\d{4})\s+([A-Z]+)/i', $line, $matches)) {
                $data['postcode'] = str_pad($matches[1], 5, '0', STR_PAD_LEFT);
                $data['city'] = strtoupper(trim($matches[2]));
                log_message('info', 'Postcode (4-digit) and city extracted: ' . $data['postcode'] . ' ' . $data['city']);
                break;
            }
        }
        
        // Smart postcode correction based on state (fix common OCR errors)
        if ($data['postcode'] && $data['state']) {
            $data['postcode'] = $this->correctPostcodeByState($data['postcode'], $data['state']);
        }

        // Parse state from lines
        $lines = explode("\n", $originalText);
        $addressLines = [];
        $foundPostcode = false;
        $postcodeIndex = -1;
        
        // Malaysian states
        $states = ['JOHOR', 'KEDAH', 'KELANTAN', 'MELAKA', 'NEGERI SEMBILAN', 'PAHANG', 
                  'PENANG', 'PERAK', 'PERLIS', 'SABAH', 'SARAWAK', 'SELANGOR', 
                  'TERENGGANU', 'KUALA LUMPUR', 'LABUAN', 'PUTRAJAYA'];
        
        // Find state in lines
        foreach ($lines as $line) {
            $line = trim($line);
            log_message('info', 'Checking line for state: "' . $line . '"');
            foreach ($states as $state) {
                if (stripos($line, $state) !== false) {
                    $data['state'] = $state;
                    log_message('info', 'Found state: ' . $state);
                    break 2;
                }
            }
        }
        
        // Find postcode line index for address extraction
        foreach ($lines as $index => $line) {
            if (preg_match('/\d{5}/', $line)) {
                $postcodeIndex = $index;
                break;
            }
        }
        
        // Parse address lines - look for KAMPUNG, JALAN, etc
        foreach ($lines as $index => $line) {
            $line = trim($line);
            if (empty($line) || strlen($line) < 3) continue;
            
            // Skip IC number line
            if (preg_match('/\d{6}[\-\s]\d{2}[\-\s]\d{4}/', $line)) continue;
            
            // Skip the entire name line (contains BIN/BINTI or matches extracted name)
            if ($data['name'] && stripos($line, $data['name']) !== false) continue;
            if (preg_match('/\b(BIN|BINTI)\b/i', $line) && !preg_match('/(KAMPUNG|JALAN|PARIT)/i', $line)) {
                // Skip if it's a name line (has BIN/BINTI without address keywords)
                continue;
            }
            
            // Skip religion, sex, citizenship, card header
            if (preg_match('/^(ISLAM|LELAKI|PEREMPUAN|WARGANEGARA|MALA|MKAD|KAD|PENGENALAN|LELANG)$/i', $line)) continue;
            
            // Extract address lines that start with KAMPUNG, JALAN, etc
            if (preg_match('/^(KAMPUNG|JALAN|JLN|TAMAN|TMN|KG|LORONG|LRG|NO|KAWASAN|BATU|BT|PARIT)/i', $line)) {
                // Clean the line
                $cleanLine = preg_replace('/[^A-Z0-9\s]/i', ' ', $line);
                $cleanLine = preg_replace('/\s+/', ' ', $cleanLine);
                $cleanLine = trim($cleanLine);
                
                // This line contains address, might also have postcode/city/state
                // Extract postcode and city if present in this line
                if (preg_match('/(\d{4,5})\s+([A-Z\s]+)$/i', $cleanLine, $pc_matches)) {
                    if (!$data['postcode']) {
                        $data['postcode'] = str_pad($pc_matches[1], 5, '0', STR_PAD_LEFT);
                        
                        // Split city and state from the match
                        $cityStatePart = trim($pc_matches[2]);
                        $cityStateWords = explode(' ', $cityStatePart);
                        
                        // Last word might be state
                        $lastWord = end($cityStateWords);
                        if (preg_match('/^(JOHOR|KEDAH|KELANTAN|MELAKA|SELANGOR|PAHANG|PERAK|PENANG|PERLIS|SABAH|SARAWAK|TERENGGANU)$/i', $lastWord)) {
                            $data['state'] = strtoupper($lastWord);
                            array_pop($cityStateWords); // Remove state from city
                        }
                        
                        // Remaining is city
                        if (!empty($cityStateWords)) {
                            $data['city'] = strtoupper(implode(' ', $cityStateWords));
                        }
                    }
                    
                    // Extract just the address part (before postcode)
                    $addressPart = preg_replace('/\d{4,5}\s+[A-Z\s]+$/i', '', $cleanLine);
                    $addressPart = trim($addressPart);
                    if ($addressPart) {
                        $addressLines[] = $addressPart;
                    }
                } else {
                    // No postcode in this line, add entire line as address
                    $addressLines[] = $cleanLine;
                }
                continue;
            }
            
            // Extract city and postcode from standalone line "18500 MACHANG"
            if (preg_match('/^(\d{4,5})\s+([A-Z\s]+)$/i', $line, $pc_matches)) {
                if (!$data['postcode']) {
                    $data['postcode'] = str_pad($pc_matches[1], 5, '0', STR_PAD_LEFT);
                    
                    // Split city and state
                    $cityStatePart = trim($pc_matches[2]);
                    $cityStateWords = explode(' ', $cityStatePart);
                    
                    // Last word might be state
                    $lastWord = end($cityStateWords);
                    if (preg_match('/^(JOHOR|KEDAH|KELANTAN|MELAKA|SELANGOR|PAHANG|PERAK|PENANG|PERLIS|SABAH|SARAWAK|TERENGGANU)$/i', $lastWord)) {
                        $data['state'] = strtoupper($lastWord);
                        array_pop($cityStateWords); // Remove state from city
                    }
                    
                    // Remaining is city
                    if (!empty($cityStateWords)) {
                        $data['city'] = strtoupper(implode(' ', $cityStateWords));
                    }
                }
                continue;
            }
            
            // Skip state line
            if (preg_match('/^(JOHOR|KEDAH|KELANTAN|MELAKA|SELANGOR|PAHANG|PERAK|PENANG|PERLIS|SABAH|SARAWAK|TERENGGANU)$/i', $line)) continue;
        }
        
        // Build address
        if (!empty($addressLines)) {
            $data['address'] = implode(', ', $addressLines);
        }
        
        // Set country for Malaysian IC (12 digits)
        if ($data['ic_number'] && strlen($data['ic_number']) == 12) {
            $data['country'] = 'Malaysia';
        }
        
        // If no city but have state, try to extract city from address lines
        if (!$data['city'] && $data['state'] && !empty($addressLines)) {
            // Last address line before state might be city
            $lastAddressLine = end($addressLines);
            if (preg_match('/([A-Z]+)$/i', $lastAddressLine, $city_match)) {
                $possibleCity = trim($city_match[1]);
                // Must be 5+ chars and not common keywords
                if (strlen($possibleCity) >= 5 && !preg_match('/KAMPUNG|JALAN|KAWASAN/i', $possibleCity)) {
                    $data['city'] = strtoupper($possibleCity);
                }
            }
        }

        return $data;
    }
    
    private function correctPostcodeByState($postcode, $state)
    {
        // Malaysian postcode ranges by state
        $statePostcodeRanges = [
            'PERLIS' => ['01', '02'],
            'KEDAH' => ['05', '06', '07', '08', '09'],
            'PENANG' => ['10', '11', '12', '13', '14'],
            'KELANTAN' => ['15', '16', '17', '18', '19'],
            'TERENGGANU' => ['20', '21', '22', '23', '24'],
            'PAHANG' => ['25', '26', '27', '28', '39'],
            'PERAK' => ['30', '31', '32', '33', '34', '35', '36'],
            'SELANGOR' => ['40', '41', '42', '43', '44', '45', '46', '47', '48', '63', '68'],
            'KUALA LUMPUR' => ['50', '51', '52', '53', '54', '55', '56', '57', '58', '59', '60'],
            'NEGERI SEMBILAN' => ['70', '71', '72', '73'],
            'MELAKA' => ['75', '76', '77', '78'],
            'JOHOR' => ['79', '80', '81', '82', '83', '84', '85', '86'],
            'SABAH' => ['87', '88', '89', '90', '91'],
            'SARAWAK' => ['93', '94', '95', '96', '97', '98'],
            'PUTRAJAYA' => ['62'],
            'LABUAN' => ['87']
        ];
        
        $firstTwo = substr($postcode, 0, 2);
        $validPrefixes = $statePostcodeRanges[strtoupper($state)] ?? [];
        
        // If postcode doesn't match state, try to correct OCR errors
        if (!in_array($firstTwo, $validPrefixes)) {
            log_message('warning', 'Postcode ' . $postcode . ' does not match state ' . $state);
            
            // Common OCR errors: 0/8, 1/7, 9/8, 6/8
            $ocrErrorMap = [
                '0' => ['8', '6'],
                '1' => ['7', '4'],
                '8' => ['0', '6', '9'],
                '9' => ['8', '4'],
                '6' => ['8', '0'],
                '7' => ['1']
            ];
            
            // Try correcting first digit
            $firstDigit = $postcode[0];
            if (isset($ocrErrorMap[$firstDigit])) {
                foreach ($ocrErrorMap[$firstDigit] as $correction) {
                    $correctedPostcode = $correction . substr($postcode, 1);
                    $correctedFirstTwo = substr($correctedPostcode, 0, 2);
                    if (in_array($correctedFirstTwo, $validPrefixes)) {
                        log_message('info', 'Corrected postcode from ' . $postcode . ' to ' . $correctedPostcode . ' based on state ' . $state);
                        return $correctedPostcode;
                    }
                }
            }
            
            // Try correcting second digit
            $secondDigit = $postcode[1];
            if (isset($ocrErrorMap[$secondDigit])) {
                foreach ($ocrErrorMap[$secondDigit] as $correction) {
                    $correctedPostcode = $postcode[0] . $correction . substr($postcode, 2);
                    $correctedFirstTwo = substr($correctedPostcode, 0, 2);
                    if (in_array($correctedFirstTwo, $validPrefixes)) {
                        log_message('info', 'Corrected postcode from ' . $postcode . ' to ' . $correctedPostcode . ' based on state ' . $state);
                        return $correctedPostcode;
                    }
                }
            }
        }
        
        return $postcode;
    }
    
    private function cleanOcrDigits($text)
    {
        // Clean common OCR errors in digits
        $replacements = [
            'O' => '0',
            'o' => '0',
            'I' => '1',
            'l' => '1',
            'i' => '1',
            'S' => '5',
            's' => '5',
            'B' => '8',
            'b' => '8',
            'f' => '1',
            'Z' => '2',
            'z' => '2',
            'g' => '9',
            'G' => '6',
            'q' => '9',
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    public function updateEmail()
    {
        // Get invitation token and new email
        $token = $this->request->getPost('token');
        $newEmail = $this->request->getPost('email');

        if (!$token || !$newEmail) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Token and email are required'
            ]);
        }

        // Validate email format
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => 'required|valid_email'
        ]);

        if (!$validation->run(['email' => $newEmail])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid email format',
                'errors' => $validation->getErrors()
            ]);
        }

        try {
            // Decode token to get invitation ID
            $invitationId = base64_decode($token);
            
            // Verify invitation exists
            $invitation = $this->invitationModel->find($invitationId);
            
            if (!$invitation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid invitation'
                ]);
            }

            // Update email
            $updated = $this->invitationModel->update($invitationId, [
                'visitor_email' => $newEmail
            ]);

            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Email updated successfully',
                    'email' => $newEmail
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update email'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Email update error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating email'
            ]);
        }
    }
}
