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

function addRecord($param){
    $formData = $param['addRecordForm'] ?? [];
    $formData['ip_address'] = getClientIpAddress();

    
    $records = new Ticket($formData);
    $result = $records->addNewTicketRecord();
    echo json_encode($result);
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

function loadAllWarehouses($param){
    $records = new Ticket($param);
    $result = $records->loadAllActiveWarehouses();
    echo json_encode($result);
}

function loadAllUsers($param){
    $records = new Ticket($param);
    $result = $records->loadAllActiveUsers();
    echo json_encode($result);
}

function loadAllCategory($param){
    $records = new Ticket($param);
    $result = $records->loadAllActiveCategory();
    echo json_encode($result);
}