<?php
namespace app\Model\Ticket;
date_default_timezone_set("Asia/Manila");

use app\Model\DbConnection\DbConfig;

class Ticket extends DbConfig{
    protected $data = null;
    public function __construct($post_data){
        parent::__construct();
        $this->data = $post_data;
    }

    public function addNewTicketRecord($filePath = null)
    {
        try {
            $conn = $this->db_connection();
            $stmt = $conn->prepare("CALL gticket.sp_saveTicketRecord(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

            $stmt->bindParam(1, $this->data['name']);
            $stmt->bindParam(2, $this->data['sbu']);
            $stmt->bindParam(3, $this->data['title']);
            $stmt->bindParam(4, $this->data['description']);
            $stmt->bindParam(5, $this->data['email']);
            $stmt->bindParam(6, $this->data['contact']);
            $stmt->bindParam(7, $this->data['ip_address']);
            $stmt->bindParam(8, $this->data['remarks']);
            $stmt->bindParam(9, $this->data['category']);
            $stmt->bindParam(10, $this->data['userCategory_text']);
            $stmt->bindParam(11, $this->data['userFullName_text']);
            $stmt->bindParam(12, $this->data['userSBU_text']);
            $stmt->bindParam(13, $this->data['ticket_type']);
            $stmt->bindParam(14, $filePath);
            $stmt->execute();

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if (!$result || !isset($result['data'])) {
                http_response_code(500);
                header('Content-Type: application/json');
                return ['error' => 'Failed to save ticket in database'];
            }

            $decoded = json_decode($result['data'], true);

            if (!$decoded || !isset($decoded['control_number'])) {
                http_response_code(500);
                header('Content-Type: application/json');
                return ['error' => 'Invalid data returned from stored procedure'];
            }

            $localTicketId = $decoded['control_number'];
            $status_id = "Open";

            $apiKey = $this->getApiKey();
            $sdpUrl = $this->getSdpUrl() . '/requests';

            $payload = [
                'request' => [
                    'subject' => $this->data['title'],
                    'description' =>
                        "Local Ticket ID: $localTicketId\n\n" .
                        "User Declared Email: " . $this->data['email'] . "\n\n" .
                        "User Declared Contact: " . $this->data['contact'] . "\n\n" .
                        "Ticket Details: \n\n" . $this->data['description'],
                    'requester' => ['id' => $this->data['name']],
                    'status' => ['name' => $status_id],
                    'service_category' => ['id' => $this->data['category']],
                    'site' => ['id' => $this->data['sbu']],
                ]
            ];

            $jsonPayload = json_encode($payload);

            $ch = curl_init($sdpUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authtoken: ' . $apiKey,
                    'Content-Type: application/x-www-form-urlencoded'
                ],
                CURLOPT_POSTFIELDS => http_build_query(['input_data' => $jsonPayload])
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($httpCode < 200 || $httpCode >= 300) {
                error_log("SDP ticket creation failed. HTTP $httpCode | Error: $error | Response: $response");
                http_response_code(500);
                header('Content-Type: application/json');
                return [
                    'error' => 'Failed to create SDP ticket',
                    'details' => json_decode($response, true) ?: $response
                ];
            }

            $sdpData = json_decode($response, true);
            $sdpId = $sdpData['request']['id'] ?? null;

            if (!$sdpId) {
                return ['error' => 'SDP ticket ID not returned'];
            }

            $attachmentIdsJson = null;

            if ($filePath && file_exists($filePath)) {
                $allowedMimeTypes = [
                    'image/png',
                    'image/jpeg',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ];
                $maxFileSize = 10 * 1024 * 1024;
                $mimeType = mime_content_type($filePath);
                $fileSize = filesize($filePath);

                if (!in_array($mimeType, $allowedMimeTypes)) {
                    return [
                        'error' => 'Invalid file type. Allowed types: PNG, JPG, PDF, DOC, DOCX',
                        'mime_type' => $mimeType
                    ];
                }

                if ($fileSize > $maxFileSize) {
                    return [
                        'error' => 'File size exceeds the 10MB limit',
                        'file_size' => $fileSize
                    ];
                }

                $attachmentUrl = $this->getSdpUrl() . '/attachments';

                $attachmentPayload = [
                    'input_data' => json_encode([
                        'attachment' => [
                            'request' => ['id' => $sdpId]
                        ]
                    ]),
                    'attachment' => new \CURLFile($filePath)
                ];

                $chAttach = curl_init($attachmentUrl);
                curl_setopt_array($chAttach, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_HTTPHEADER => [
                        'Authtoken: ' . $apiKey
                    ],
                    CURLOPT_POSTFIELDS => $attachmentPayload
                ]);

                $attachmentResponse = curl_exec($chAttach);
                $attachmentHttpCode = curl_getinfo($chAttach, CURLINFO_HTTP_CODE);
                $attachmentError = curl_error($chAttach);
                curl_close($chAttach);

                if ($attachmentHttpCode < 200 || $attachmentHttpCode >= 300) {
                    error_log("SDP attachment upload failed. HTTP $attachmentHttpCode | Error: $attachmentError | Response: $attachmentResponse");
                    return [
                        'error' => 'Failed to upload attachment',
                        'details' => json_decode($attachmentResponse, true) ?: $attachmentResponse
                    ];
                }

                $attachmentResult = json_decode($attachmentResponse, true);
                $attachmentId = $attachmentResult['attachment']['id'] ?? null;

                if ($attachmentId !== null) {
                    $attachmentIdsJson = json_encode([(string)$attachmentId]);
                }
            }

            $technician = null;
            $resolution = null;

            $stmtSdp = $conn->prepare("CALL sp_update_ticket_record(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

            $stmtSdp->bindParam(1, $sdpId);
            $stmtSdp->bindParam(2, $localTicketId);
            $stmtSdp->bindParam(3, $this->data['name']);
            $stmtSdp->bindParam(4, $this->data['sbu']);
            $stmtSdp->bindParam(5, $this->data['title']);
            $stmtSdp->bindParam(6, $this->data['description']);
            $stmtSdp->bindParam(7, $this->data['email']);
            $stmtSdp->bindParam(8, $this->data['contact']);
            $stmtSdp->bindParam(9, $this->data['ip_address']);
            $stmtSdp->bindParam(10, $this->data['remarks']);
            $stmtSdp->bindParam(11, $this->data['category']);
            $stmtSdp->bindParam(12, $this->data['userCategory_text']);
            $stmtSdp->bindParam(13, $this->data['userFullName_text']);
            $stmtSdp->bindParam(14, $this->data['userSBU_text']);
            $stmtSdp->bindParam(15, $status_id);
            $stmtSdp->bindParam(16, $attachmentIdsJson);
            $stmtSdp->bindParam(17, $technician);
            $stmtSdp->bindParam(18, $resolution);
            $stmtSdp->execute();

            $resulttoAPI = $stmtSdp->fetch(\PDO::FETCH_ASSOC);

            if (($resulttoAPI['result'] ?? null) != 1) {
                return [
                    'error' => 'Stored procedure did not return expected success result',
                    'debug_result' => $resulttoAPI
                ];
            }

            http_response_code(201);
            header('Content-Type: application/json');
            return [
                'local_ticket_id' => $localTicketId,
                'sdp_id' => $sdpId,
                'result' => $decoded
            ];

        } catch (\PDOException $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            return [
                'error' => 'Database error',
                'details' => $e->getMessage()
            ];
        }
    }



    public function loadAllActiveWarehouses(){
        $conn = $this->db_connection();
        $stmt = $conn->prepare("call gticket.sp_getAllWarehouses()");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $option = ($result['option'] == NULL) ? '' : join('',json_decode($result['option']));
        return [
            'data' => ($result['data'] == NULL) ? [] : json_decode($result['data']),
            'option' => $option
        ];
    }

    public function syncUsertoDB($ipAddress = "UNKNOWN") {
        $sdpUsers = $this->loadAllActiveUsers();

        if (empty($sdpUsers['users'])) {
            error_log("syncUsertoDB: No users fetched from SDP.");
            return ['result' => 'Failure', 'message' => 'No users fetched'];
        }

        $conn = $this->db_connection();
        $updatedCount = 0;

        foreach ($sdpUsers['users'] as $user) {
            $userId = $user['id'];
            $userName = $user['name'];
            $userEmail = $user['email_id'];

            $stmt = $conn->prepare("SELECT sdp_user_name FROM tbl_sdp_users WHERE sdp_id = :user_id");
            $stmt->execute([':user_id' => $userId]);
            $existing = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($existing && $existing['sdp_user_name'] === $userName) {
                continue;
            }

            $this->insertUserToDB($user);
            $updatedCount++;
        }

        $logStmt = $conn->prepare("CALL sp_logs_record(:count, :ipaddress)");
        $logStmt->execute([
            ':count' => "Fetched User Data from SDP API. User Record Updated: " . $updatedCount,
            ':ipaddress' => $ipAddress
        ]);

        if ($updatedCount > 0) {
            return [
                'result' => 'Success',
                'count' => $updatedCount
            ];
        } else {
            return [
                'result' => 'NoUpdate',
                'count' => $updatedCount
            ];
        }
    }

    private function insertUserToDB($user){
        try {
            $conn = $this->db_connection();
            $stmt = $conn->prepare("REPLACE INTO tbl_sdp_users (sdp_id, sdp_user_name, sdp_email) VALUES (:user_id, :user_name, :user_email)");
            $stmt->execute([
                ':user_id' => $user['id'],
                ':user_name' => $user['name'],
                ':user_email' => $user['email_id']
            ]);
        } catch (\PDOException $e) {
            error_log("❌ Failed to insert site: " . $user['id'] . " - " . $e->getMessage());
        }
    }

    public function loadAllActiveUsersFromDB(){
        $conn = $this->db_connection();
        $stmt = $conn->prepare("call gticket.sp_getAllUsers()");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $option = ($result['option'] == NULL) ? '' : join('',json_decode($result['option']));
        return [
            'data' => ($result['data'] == NULL) ? [] : json_decode($result['data']),
            'option' => $option
        ];
    }

    public function loadAllActiveUsers(): array {
        $apiKey = $this->getApiKey();
        $listInfo = [
            "list_info" => [
                "sort_field" => "name",
                "row_count" => 1000,
                "start_index" => 1
            ]
        ];

        $jsonPayload = json_encode($listInfo);
        $encodedURI = rawurlencode($jsonPayload);
        $sdpUrl = $this->getSdpUrl() . '/users?input_data=' . $encodedURI;
        
        $ch = curl_init($sdpUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authtoken: ' . $apiKey,
                'Accept: application/json'
            ]
        ]);
    
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
    
        if ($httpCode >= 200 && $httpCode < 300) {
            $data = json_decode($response, true);
            if (!isset($data['users'])) {
                return [
                    'success' => false,
                    'error' => 'Missing users in response',
                    'raw_response' => $data
                ];
            }

            $users = [];
            foreach ($data['users'] as $user) {
                if (isset($user['id'], $user['name'])) {
                    $users[] = [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email_id' => $user['email_id']
                    ];
                }
            }
    
            return [
                'success' => true,
                'users' => $users
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Failed to fetch users from SDP',
                'http_code' => $httpCode,
                'details' => json_decode($response, true) ?: $response,
                'curl_error' => $error
            ];
        }
    }

    public function syncCategorytoDB($ipAddress = "UNKNOWN") {
        $sdpCategories = $this->loadAllActiveCategory();

        if (empty($sdpCategories['categories'])) {
            error_log("syncUsertoDB: No categories fetched from SDP.");
            return ['result' => 'Failure', 'message' => 'No categories fetched'];
        }

        $conn = $this->db_connection();
        $updatedCount = 0;

        foreach ($sdpCategories['categories'] as $category) {
            $categoryId = $category['id'];
            $categoryName = $category['name'];

            $stmt = $conn->prepare("SELECT sdp_categories FROM tbl_sdp_categories WHERE sdp_id = :category_id");
            $stmt->execute([':category_id' => $categoryId]);
            $existing = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($existing && $existing['sdp_categories'] === $categoryName) {
                continue;
            }

            $this->insertCategoryToDB($category);
            $updatedCount++;
        }

        $logStmt = $conn->prepare("CALL sp_logs_record(:count, :ipaddress)");
        $logStmt->execute([
            ':count' => "Fetched Category Data from SDP API. Category Record Updated: " . $updatedCount,
            ':ipaddress' => $ipAddress
        ]);

        if ($updatedCount > 0) {
            return [
                'result' => 'Success',
                'count' => $updatedCount
            ];
        } else {
            return [
                'result' => 'NoUpdate',
                'count' => $updatedCount
            ];
        }
    }

    private function insertCategoryToDB($category){
        try {
            $conn = $this->db_connection();
            $stmt = $conn->prepare("REPLACE INTO tbl_sdp_categories (sdp_id, sdp_categories) VALUES (:category_id, :category_name)");
            $stmt->execute([
                ':category_id' => $category['id'],
                ':category_name' => $category['name']
            ]);
        } catch (\PDOException $e) {
            error_log("❌ Failed to insert category: " . $category['id'] . " - " . $e->getMessage());
        }
    }

    public function loadAllActiveWarehousesFromDB(){
        $conn = $this->db_connection();
        $stmt = $conn->prepare("call gticket.sp_getAllCategories()");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $option = ($result['option'] == NULL) ? '' : join('',json_decode($result['option']));
        return [
            'data' => ($result['data'] == NULL) ? [] : json_decode($result['data']),
            'option' => $option
        ];
    }

    public function loadAllActiveCategory(): array {
        $apiKey = $this->getApiKey();
        $sdpUrl = ($this->getSdpUrl()) . '/service_categories';
    
        $ch = curl_init($sdpUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authtoken: ' . $apiKey,
                'Accept: application/json'
            ]
        ]);
    
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
    
        if ($httpCode >= 200 && $httpCode < 300) {
            $data = json_decode($response, true);
    
            if (!isset($data['service_categories'])) {
                return [
                    'success' => false,
                    'error' => 'Missing service categories in response',
                    'raw_response' => $data
                ];
            }
    
            $categories = [];
            foreach ($data['service_categories'] as $category) {
                if (empty($category['deleted']) && isset($category['id'], $category['name'])) {
                    $categories[] = [
                        'id' => $category['id'],
                        'name' => $category['name']
                    ];
                }
            }
    
            return [
                'success' => true,
                'categories' => $categories
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Failed to fetch service categories from SDP',
                'http_code' => $httpCode,
                'details' => json_decode($response, true) ?: $response,
                'curl_error' => $error
            ];
        }
    } 

    public function loadAllActiveCategoryFromDB(){
        $conn = $this->db_connection();
        $stmt = $conn->prepare("call gticket.sp_getAllCategories()");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $option = ($result['option'] == NULL) ? '' : join('',json_decode($result['option']));
        return [
            'data' => ($result['data'] == NULL) ? [] : json_decode($result['data']),
            'option' => $option
        ];
    }

    public function getTicketStatusByNumber($ipAddress = null) {
        $conn = $this->db_connection();

        try {
            $ip = $ipAddress ?? ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
            
            $stmt = $conn->prepare("CALL gticket.sp_getTicketDetails(?, ?)");
            $stmt->bindParam(1, $this->data['id']);
            $stmt->bindParam(2, $ip);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if (!$result || empty($result['localTicketID'])) {
                http_response_code(500);
                return ['error' => 'Failed to retrieve ticket details from the database'];
            }

            if (!empty($result['serviceID'])) {
                $ticketId = $result['serviceID'];
            } else {
                http_response_code(500);
                return ['error' => 'No valid ticket identifier found to query SDP API'];
            }

            $apiKey = $this->getApiKey();
            $sdpUrl = $this->getSdpUrl() . '/requests/' . $ticketId;

            $ch = curl_init($sdpUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authtoken: ' . $apiKey,
                    'Content-Type: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                $ticketData = json_decode($response, true);
                if (isset($ticketData['request'])) {
                    $status = $ticketData['request']['status'] ?? null;
                    $result['statusid'] = $status['id'] ?? null;
                    $result['statusname'] = $status['name'] ?? null;
                    $result['statuscolor'] = $status['color'] ?? null;
                    $createdTime = $ticketData['request']['created_time']['display_value'] ?? null;
                    $lastUpdatedTime = $ticketData['request']['last_updated_time']['display_value'] ?? null;

                    $result['created_time'] = $createdTime;
                    $result['last_updated_time'] = $lastUpdatedTime;

                    $technician = $ticketData['request']['technician'] ?? null;
                    if ($technician && is_array($technician)) {
                        $result['technician'] = [
                            'name' => $technician['name'] ?? null,
                            'id' => $technician['id'] ?? null,
                            'email_id' => $technician['email_id'] ?? null,
                        ];
                    } else {
                        $result['technician'] = null;
                    }

                    $resolution = $ticketData['request']['resolution'] ?? null;
                    if ($resolution && is_array($resolution)) {
                        $result['resolution'] = [
                            'content' => $resolution['content'] ?? null
                        ];
                    } else {
                        $result['resolution'] = null;
                    }

                    $attachments = $ticketData['request']['attachments'] ?? [];
                    $attachmentIds = [];

                    if (!empty($attachments) && is_array($attachments)) {
                        foreach ($attachments as $attachment) {
                            if (isset($attachment['id'])) {
                                $attachmentIds[] = $attachment['id'];
                            }
                        }
                    }

                    $attachmentIdsJson = !empty($attachmentIds) ? json_encode($attachmentIds) : null;

                    $updateStmt = $conn->prepare("CALL sp_update_ticket_record(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

                    $updateStmt->bindValue(1, $ticketId);
                    $updateStmt->bindValue(2, $result['localTicketID']);
                    $updateStmt->bindValue(3, $result['name'] ?? 0, \PDO::PARAM_INT);
                    $updateStmt->bindValue(4, $result['warehouseID'] ?? 0, \PDO::PARAM_INT);
                    $updateStmt->bindValue(5, $result['title'] ?? '');
                    $updateStmt->bindValue(6, $result['description'] ?? '');
                    $updateStmt->bindValue(7, $result['email'] ?? '');
                    $updateStmt->bindValue(8, $result['contact'] ?? '');
                    $updateStmt->bindValue(9, $ip);
                    $updateStmt->bindValue(10, $result['remarks']?? '');
                    $updateStmt->bindValue(11, $result['category'] ?? 0, \PDO::PARAM_INT);
                    $updateStmt->bindValue(12, $result['category_name'] ?? '');
                    $updateStmt->bindValue(13, $result['user_FullName'] ?? '');
                    $updateStmt->bindValue(14, $result['warehouseName'] ?? '');
                    $updateStmt->bindValue(15, $result['statusname'] ?? 0, \PDO::PARAM_INT);
                    $updateStmt->bindValue(16, $attachmentIdsJson, is_null($attachmentIdsJson) ? \PDO::PARAM_NULL : \PDO::PARAM_STR);
                    $updateStmt->bindValue(17, $result['technician']['name'] ?? '');
                    $updateStmt->bindValue(18, $result['resolution']['content'] ?? '');
                    $updateStmt->execute();
                    $insertResult = $updateStmt->fetch(\PDO::FETCH_ASSOC);
                    $updateStmt->closeCursor();
                    $combinedResult = array_merge($result, [
                            'trail_id' => $insertResult['trail_id'] ?? null,
                            'result' => $insertResult['result'] ?? null,
                            'hashCode' => $insertResult['hashCode'] ?? null,
                            'created_time' => $result['created_time'],
                            'last_updated_time' => $result['last_updated_time']
                    ]);

                    return $combinedResult;
                } else {
                    return ['error' => 'Status not found in the ticket data'];
                }
            } else {
                error_log("SDP API request failed. HTTP Code: $httpCode | Error: $error | Response: $response");
                return [
                    'error' => 'Failed to retrieve ticket status from SDP API',
                    'details' => $response
                ];
            }
        } catch (\PDOException $e) {
            http_response_code(500);
            return ['error' => 'Database error: ' . $e->getMessage()];
        } catch (\Exception $e) {
            http_response_code(500);
            return ['error' => 'Unexpected error: ' . $e->getMessage()];
        }
    }

    public function syncSDPSites($ipAddress = "UNKNOWN") {
        $sdpSites = $this->fetchSitesFromSDP();

        if (empty($sdpSites['data'])) {
            error_log("syncSDPSites: No sites fetched from SDP.");
            return ['result' => 'Failure', 'message' => 'No sites fetched'];
        }

        $conn = $this->db_connection();
        $updatedCount = 0;

        foreach ($sdpSites['data'] as $site) {
            $siteId = $site['id'];
            $siteName = $site['name'];

            $stmt = $conn->prepare("SELECT sdp_site_name FROM tbl_sdp_sites WHERE sdp_id = :site_id");
            $stmt->execute([':site_id' => $siteId]);
            $existing = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($existing && $existing['sdp_site_name'] === $siteName) {
                continue;
            }

            $this->insertSiteToDB($site);
            $updatedCount++;
        }

        $logStmt = $conn->prepare("CALL sp_logs_record(:count, :ipaddress)");
        $logStmt->execute([
            ':count' => "Fetched Site Data from SDP API. Site Record Updated: " . $updatedCount,
            ':ipaddress' => $ipAddress
        ]);

        if ($updatedCount > 0) {
            return [
                'result' => 'Success',
                'count' => $updatedCount
            ];
        } else {
            return [
                'result' => 'NoUpdate',
                'count' => $updatedCount
            ];
        }
    }

    private function insertSiteToDB($site){
        try {
            $conn = $this->db_connection();
            $stmt = $conn->prepare("REPLACE INTO tbl_sdp_sites (sdp_id, sdp_site_name) VALUES (:site_id, :site_name)");
            $stmt->execute([
                ':site_id' => $site['id'],
                ':site_name' => $site['name']
            ]);
        } catch (\PDOException $e) {
            error_log("❌ Failed to insert site: " . $site['id'] . " - " . $e->getMessage());
        }
    }

    public function fetchSitesFromSDP(): array {
        $apiKey = $this->getApiKey();

        $listInfo = [
            "list_info" => [
                "sort_field" => "name",
                "row_count" => 40,
                "start_index" => 1
            ]
        ];

        $jsonPayload = json_encode($listInfo);
        $encodedURI = rawurlencode($jsonPayload);
        $sdpUrl = $this->getSdpUrl() . '/sites?input_data=' . $encodedURI;

        $headers = [
            "TECHNICIAN_KEY: $apiKey",
            "Accept: application/vnd.manageengine.sdp.v3+json"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sdpUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            error_log("fetchSitesFromSDP: HTTP $httpCode response error");
            return [];
        }

        $data = json_decode($response, true);
        if (!isset($data['sites'])) {
            return [];
        }

        $sites = $data['sites'];
        $optionHtml = '';
        foreach ($sites as $site) {
            $id = htmlspecialchars($site['id'] ?? '');
            $name = htmlspecialchars($site['name'] ?? '');
            $optionHtml .= "<option value=\"$id\">$name</option>";
        }

        return [
            'data' => $sites,
            'option' => $optionHtml
        ];
    }

    public function getSitesFromDB(): array {
        $conn = $this->db_connection();
        $stmt = $conn->query("SELECT sdp_id AS value, sdp_site_name AS label FROM tbl_sdp_sites ORDER BY sdp_site_name ASC");
        $sites = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $optionHtml = '';
        foreach ($sites as $site) {
            $optionHtml .= "<option value=\"{$site['value']}\">{$site['label']}</option>";
        }

        return [
            'data' => $sites,
            'option' => $optionHtml
        ];
    }

    public function ViewAllTicketDetails(){
        $ticketHash = $this->data['id'];
        if (!$ticketHash) {
            throw new \Exception("Missing ticket hash");
        }

        $conn = $this->db_connection();

        $stmt = $conn->prepare("CALL gticket.sp_getAlldetailsforTicketPrint(?)");
        $stmt->bindParam(1, $ticketHash);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        if (isset($result['data']) && !empty($result['data'])) {
            $decodedData = json_decode($result['data'], true);
            if (json_last_error() == JSON_ERROR_NONE) {
                return ['data' => $decodedData];
            } else {
                return ['data' => []];
            }
        } else {
            return ['data' => []];
        }
    }

}