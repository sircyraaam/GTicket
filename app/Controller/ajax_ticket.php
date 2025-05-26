<?php
session_start();
require_once '../../vendor/autoload.php';
date_default_timezone_set("Asia/Manila");

use app\Model\Ticket\Ticket;

if(isset($_POST['function'])){
    $param = array_merge($_POST,$_FILES);
    $param = array_merge($param);
    $_POST['function']($param);
}


function addRecord() {
    $formData = $_POST;
    $files = $_FILES;

    $uploadDir = '../../assets/img/uploads/';
    $uploadedFilePath = null;

    if (isset($files['attachment']) && $files['attachment']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $files['attachment']['tmp_name'];
        $originalName = basename($files['attachment']['name']);
        $uploadedFilePath = $uploadDir . time() . '_' . $originalName;

        if (!move_uploaded_file($tmpName, $uploadedFilePath)) {
            echo json_encode(['error' => 'Failed to save uploaded file']);
            return;
        }
    }

    $formData['ip_address'] = getClientIpAddress();

    $ticket = new Ticket($formData);
    $result = $ticket->addNewTicketRecord($uploadedFilePath);

    echo json_encode($result);

    if ($uploadedFilePath && file_exists($uploadedFilePath)) {
        unlink($uploadedFilePath); 
    }
}


function getClientIpAddress() {
    $ipKeys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];

    foreach ($ipKeys as $key) {
        if (!empty($_SERVER[$key])) {
            $ipList = explode(',', $_SERVER[$key]);
            foreach ($ipList as $ip) {
                $cleanIp = trim($ip);
                if (filter_var($cleanIp, FILTER_VALIDATE_IP)) {
                    return $cleanIp;
                }
            }
        }
    }

    return 'UNKNOWN';
}

function syncWarehousestoLocalDB($param) {
    $records = new Ticket($param);
    $ipAddress = getClientIpAddress();

    $siteResult = $records->syncSDPSites($ipAddress);
    $userResult = $records->syncUsertoDB($ipAddress);
    $categoryResult = $records->syncCategorytoDB($ipAddress);

    $result = [
        'site_sync' => $siteResult,
        'user_sync' => $userResult,
        'category_sync' => $categoryResult
    ];

    echo json_encode($result);
}

function loadAllWarehouses($param){
    $records = new Ticket($param);
    $result = $records->loadAllActiveWarehouses();
    echo json_encode($result);
}

function loadAllUsers($param){
    $records = new Ticket($param);
    $result = $records->loadAllActiveUsersFromDB();
    echo json_encode($result);
}

function loadAllCategory($param){
    $records = new Ticket($param);
    $result = $records->loadAllActiveCategoryFromDB();
    echo json_encode($result);
}

function searchTicketByNumber($param){
    $records = new Ticket($param);
    $ipAddress = getClientIpAddress();
    $result = $records->getTicketStatusByNumber($ipAddress);
    echo json_encode($result);
}