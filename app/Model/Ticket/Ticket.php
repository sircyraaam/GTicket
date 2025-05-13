<?php
namespace app\Model\Ticket;
date_default_timezone_set("Asia/Manila");

use app\Model\DbConnection\DbConfig;

class Ticket extends DbConfig{
    protected $data = null;
    public function __construct($post_data){
        $this->data = $post_data;
    }

    public function addNewTicketRecord() {
        try {
            $conn = $this->db_connection();
            $stmt = $conn->prepare("CALL gticket.sp_saveTicketRecord(?,?,?,?,?,?,?,?,?,?,?,?)");
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
            $stmt->bindParam(12, $this->data['ticket_type']);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if (!$result || !isset($result['data'])) {
                http_response_code(500);
                header('Content-Type: application/json');
                return (['error' => 'Failed to save ticket in database']);
            }
    
            $decoded = json_decode($result['data'], true);

            if (!$decoded || !isset($decoded['control_number'])) {
                http_response_code(500);
                header('Content-Type: application/json');
                return (['error' => 'Invalid data returned from stored procedure']);
            }
    
            $localTicketId = $decoded['control_number'];
    
            $apiKey = 'EA50B121-A98B-4A95-9700-D3FFEEAFB17F';
            $sdpUrl = 'http://103.21.14.107:9004/api/v3/requests';
    
            $payload = [
                'request' => [
                    'subject' => $this->data['title'],
                    'description' => "Local Ticket ID: $localTicketId\n\n" .
                                     "User Declared Email: " . $this->data['email'] . "\n\n" .
                                     "User Declared Contact: " . $this->data['contact'] . "\n\n" .
                                     "Ticket Details: " . "\n\n" .
                                     $this->data['description'],
                    'requester' => ['id' => $this->data['name']],
                    'status' => ['name' => 'Open'],
                    'service_category' => ['id' => $this->data['category']],
                ]
            ];
    
            $formData = http_build_query(['input_data' => json_encode($payload)]);
    
            $ch = curl_init($sdpUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authtoken: ' . $apiKey,
                    'Content-Type: application/x-www-form-urlencoded'
                ],
                CURLOPT_POSTFIELDS => $formData
            ]);
    
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                $sdpData = json_decode($response, true);
                $sdpId = $sdpData['request']['id'] ?? null;
            

                    $stmtSdp = $conn->prepare("CALL sp_update_ticket_record(?,?,?,?,?,?,?,?,?,?,?,?,?)");
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
                    return ([
                        'local_ticket_id' => $localTicketId,
                        'sdp_id' => $sdpId,
                        'result' => $decoded
                    ]);
                
            } else {
                error_log("SDP ticket creation failed. HTTP $httpCode | Error: $error | Response: $response");
                http_response_code(500);
                header('Content-Type: application/json');
                return ([
                    'error' => 'Failed to create SDP ticket',
                    'details' => json_decode($response, true) ?: $response
                ]);
            }
    
        } catch (\PDOException $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            return ([
                'error' => 'Database error',
                'details' => $e->getMessage()
            ]);
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

    public function loadAllActiveUsers(){
        $apiKey = 'EA50B121-A98B-4A95-9700-D3FFEEAFB17F';
        $sdpUrl = 'http://103.21.14.107:9004/api/v3/users';
    
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
                        'name' => $user['name']
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

    public function loadAllActiveCategory(){
        $apiKey = 'EA50B121-A98B-4A95-9700-D3FFEEAFB17F';
        $sdpUrl = 'http://103.21.14.107:9004/api/v3/service_categories';
    
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

    public function getTicketStatusFromSDP($ticketId) {
        $apiKey = 'EA50B121-A98B-4A95-9700-D3FFEEAFB17F'; // Replace with your actual API Key
        $sdpUrl = 'http://103.21.14.107:9004/api/v3/requests/'.$ticketId; // Adjust the URL based on your SDP API
    
        // Initialize cURL session
        $ch = curl_init($sdpUrl);
        
        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,  // To return the response as a string
            CURLOPT_HTTPHEADER => [
                'Authtoken: ' . $apiKey,  // API Key in the header
                'Content-Type: application/json'
            ]
        ]);
    
        // Execute the cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
    
        // Check if the response is successful
        if ($httpCode >= 200 && $httpCode < 300) {
            // Decode the JSON response from the API
            $ticketData = json_decode($response, true);
            
            if (isset($ticketData['request']['status'])) {
                // Return the ticket status
                return $ticketData['request']['status'];
            } else {
                // Return error if status is not found in the response
                return [
                    'error' => 'Status not found in the ticket data'
                ];
            }
        } else {
            // Log and return error if the API request fails
            error_log("SDP API request failed. HTTP Code: $httpCode | Error: $error | Response: $response");
            return [
                'error' => 'Failed to retrieve ticket status from SDP API',
                'details' => $response
            ];
        }
    }
    

}