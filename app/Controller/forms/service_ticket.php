<?php
session_start();
date_default_timezone_set("Asia/Manila");
require_once '../../../vendor/autoload.php';
require_once ('../../../assets/vendor/tcpdf/tcpdf.php');

use app\Model\Ticket\Ticket;
use app\Model\DbConnection\DbConfig;

$param = array_merge($_POST);

$id = $_GET['id'];
$db = new DbConfig();
$conn = $db->db_connection();

?>