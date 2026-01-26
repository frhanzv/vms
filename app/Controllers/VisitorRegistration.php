<?php

namespace App\Controllers;

use App\Models\InvitationModel;
use App\Models\InvitationScheduleModel;
use App\Models\LocationModel;
use App\Models\CountryModel;
use App\Models\StateModel;
use App\Models\CityModel;
use App\Models\CompanyModel;

class VisitorRegistration extends BaseController
{
    protected $invitationModel;
    protected $scheduleModel;
    protected $locationModel;
    protected $countryModel;
    protected $stateModel;
    protected $cityModel;
    protected $companyModel;

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
            // Get company visiting (could be multiple)
            $companyVisiting = $this->request->getPost('company_visiting');
            $companyVisitingStr = is_array($companyVisiting) ? implode(', ', $companyVisiting) : $companyVisiting;

            // Prepare visitor data - save ALL fields
            $visitorData = [
                'full_name' => $this->request->getPost('full_name'),
                'ic_passport' => $this->request->getPost('ic_number'),
                'contact' => $this->request->getPost('contact_number'),
                'visitor_email' => $this->request->getPost('email'),
                'date_of_birth' => $this->request->getPost('date_of_birth'),
                'sex' => $this->request->getPost('sex'),
                'resident' => $this->request->getPost('resident'),
                'address' => $this->request->getPost('address') ?? '',
                'postcode' => $this->request->getPost('postcode') ?? '',
                'city' => $this->request->getPost('city') ?? '',
                'state' => $this->request->getPost('state') ?? '',
                'country' => $this->request->getPost('country') ?? '',
                'company' => $this->request->getPost('company_name') ?? '',
                'registration_no' => $this->request->getPost('company_registration') ?? '',
                'vehicle_registration' => $this->request->getPost('vehicle_registration') ?? '',
                'staff_id' => $this->request->getPost('staff_id') ?? '',
                'host_contact' => $this->request->getPost('host_contact') ?? '',
                'company_visited' => $this->request->getPost('company_visited') ?? '',
                'location' => $companyVisitingStr,
                'reason' => $this->request->getPost('visit_reason') ?? '',
                'status' => 'Approved'
            ];

            // Insert or update invitation record
            $invitationId = $this->invitationModel->insert($visitorData);

            if (!$invitationId) {
                throw new \Exception('Failed to create invitation record');
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
            // Use EasyOCR Python script for better accuracy
            $pythonPath = ROOTPATH . '.venv/Scripts/python.exe';
            $scriptPath = ROOTPATH . 'ocr_mykad.py';
            
            // Check if Python and script exist
            if (!file_exists($pythonPath)) {
                log_message('error', 'Python not found at: ' . $pythonPath);
                throw new \Exception('EasyOCR Python environment not configured');
            }
            
            if (!file_exists($scriptPath)) {
                log_message('error', 'OCR script not found at: ' . $scriptPath);
                throw new \Exception('EasyOCR script not found');
            }
            
            // Execute Python EasyOCR script
            $command = '"' . $pythonPath . '" "' . $scriptPath . '" "' . $imagePath . '" 2>nul';
            
            log_message('info', 'Executing EasyOCR: ' . $command);
            
            $output = shell_exec($command);
            
            // Find JSON in output (skip any progress bars)
            $jsonStart = strpos($output, '{');
            $jsonEnd = strrpos($output, '}');
            
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonStr = substr($output, $jsonStart, $jsonEnd - $jsonStart + 1);
                $result = json_decode($jsonStr, true);
            } else {
                $result = null;
            }
            
            log_message('info', 'EasyOCR Parsed Result: ' . json_encode($result));
            
            if (!$result || !isset($result['success'])) {
                log_message('error', 'Invalid EasyOCR response: ' . $output);
                throw new \Exception('Failed to parse EasyOCR output');
            }
            
            if (!$result['success']) {
                log_message('error', 'EasyOCR error: ' . ($result['error'] ?? 'Unknown error'));
                throw new \Exception('EasyOCR failed: ' . ($result['error'] ?? 'Unknown error'));
            }
            
            $text = $result['text'] ?? '';
            
            log_message('info', 'EasyOCR Text: ' . json_encode($text));
            log_message('info', 'EasyOCR Confidence: ' . ($result['confidence'] ?? 'unknown'));
            log_message('info', 'EasyOCR Text Lines: ' . json_encode($result['lines'] ?? []));
            
            $data = $this->parseMyKadText($text);
            
            // Log what was extracted
            log_message('info', 'Extracted Data: ' . json_encode($data));
            
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

        // Keep original text for line-based parsing
        $originalText = $text;
        
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
            
            // Skip IC number, postcode, religion, sex, state, citizenship, card type
            if (preg_match('/\d{6}[\-\s]\d{2}[\-\s]\d{4}|\d{5}\s+[A-Z]+|^(ISLAM|LELAKI|PEREMPUAN|WARGANEGARA|KELANTAN|JOHOR|MALA|MKAD)$/i', $line)) continue;
            
            // Pattern: "FIRSTNAME BIN/BINTI LASTNAME" in one line
            if (preg_match('/^([A-Z\s]+?)\s+(BIN|BINTI)\s+([A-Z\s]+?)$/i', $line, $matches)) {
                $data['name'] = strtoupper(trim($matches[1] . ' ' . $matches[2] . ' ' . $matches[3]));
                $foundBin = true;
                break;
            }
        }
        
        // Second pass: look for split name across lines
        if (!$foundBin) {
            foreach ($lines as $i => $line) {
                $line = trim($line);
                
                // Found BIN/BINTI on its own line OR as start of line "BIN WAN KAR MIZI"
                if (preg_match('/^(BIN|BINTI)\s+(.+)$/i', $line, $matches)) {
                    $foundBin = true;
                    
                    // Look for first name in previous line
                    if ($i > 0) {
                        $prevLine = trim($lines[$i-1]);
                        // Must be 3+ chars, no digits, not keywords
                        if (strlen($prevLine) >= 3 && 
                            !preg_match('/\d|KAMPUNG|JALAN|ISLAM|LELAKI|PEREMPUAN|WARGANEGARA|KELANTAN|JOHOR|MALA|MKAD/i', $prevLine)) {
                            $nameParts[] = $prevLine;
                        }
                    }
                    
                    // Add "BIN/BINTI LASTNAME"
                    $nameParts[] = $line;
                    break;
                }
                
                // Found standalone BIN/BINTI
                if (preg_match('/^(BIN|BINTI)$/i', $line)) {
                    $foundBin = true;
                    
                    // Look for first name in previous 1-2 lines
                    for ($j = $i - 1; $j >= max(0, $i - 2); $j--) {
                        $prevLine = trim($lines[$j]);
                        if (strlen($prevLine) >= 3 && 
                            !preg_match('/\d|KAMPUNG|JALAN|ISLAM|LELAKI|PEREMPUAN|WARGANEGARA|KELANTAN|JOHOR|MALA|MKAD/i', $prevLine)) {
                            array_unshift($nameParts, $prevLine);
                        }
                    }
                    
                    $nameParts[] = $line; // Add BIN/BINTI
                    
                    // Look for last name in next 1-2 lines
                    for ($j = $i + 1; $j <= min(count($lines) - 1, $i + 2); $j++) {
                        $nextLine = trim($lines[$j]);
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

        // Extract postcode and city (format: "82010 PONTIAN" or "18500 MACHANG")
        if (preg_match('/(\d{5})\s+([A-Z]+)/i', $originalText, $matches)) {
            $data['postcode'] = $matches[1];
            $data['city'] = strtoupper(trim($matches[2]));
        }
        
        // Alternative: just postcode without city
        if (!$data['postcode'] && preg_match('/\b(\d{5})\b/', $originalText, $matches)) {
            $data['postcode'] = $matches[1];
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
            
            // Skip name components
            if (preg_match('/^(BIN|BINTI|MOHAMAD|AHMAD|MUHAMMAD|WAN|AHMAD|FARHAN)$/i', $line)) continue;
            
            // Skip religion, sex, citizenship, card header
            if (preg_match('/^(ISLAM|LELAKI|PEREMPUAN|WARGANEGARA|MALA|MKAD|KAD|PENGENALAN)$/i', $line)) continue;
            
            // Extract address lines (KAMPUNG, JALAN, etc)
            if (preg_match('/^(KAMPUNG|JALAN|JLN|TAMAN|TMN|KG|LORONG|LRG|NO|KAWASAN|BATU|BT|PARIT)/i', $line)) {
                $addressLines[] = $line;
                continue;
            }
            
            // Extract city and postcode from combined line "18500 MACHANG"
            if (preg_match('/(\d{5})\s+([A-Z]+)/i', $line)) {
                if (!$data['postcode']) {
                    preg_match('/(\d{5})\s+([A-Z]+)/i', $line, $pc_matches);
                    $data['postcode'] = $pc_matches[1];
                    $data['city'] = strtoupper($pc_matches[2]);
                }
                continue;
            }
            
            // Skip state line
            if (preg_match('/^(JOHOR|KEDAH|KELANTAN|MELAKA|SELANGOR|PAHANG|PERAK|PENANG|PERLIS|SABAH|SARAWAK|TERENGGANU)$/i', $line)) continue;
            
            // Collect other potential address lines (capitalized text before state)
            if ($postcodeIndex > 0 && $index < $postcodeIndex) {
                // Look for capitalized address lines
                if (preg_match('/^[A-Z0-9\\s]{5,}$/i', $line)) {
                    $addressLines[] = $line;
                }
            }
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
