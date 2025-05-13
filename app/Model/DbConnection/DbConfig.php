<?php
namespace app\Model\DbConnection;
use \PDO;

class DbConfig{
    
    private $db_server = 'localhost';
    private $db_port = '3306';
    private $db_name = 'gticket';
    private $db_username = 'root';
    private $db_password = 'GILIwms_@2022';
    public $dbConn = null;

    public function db_connection(){
        try {
            $dsn = "mysql:host={$this->db_server};port={$this->db_port};dbname={$this->db_name};charset=utf8mb4";
            $this->dbConn = new PDO($dsn, $this->db_username, $this->db_password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
            ]);

            return $this->dbConn;
            echo "Connected successfully";
        }catch (\PDOException $e){
            echo "Connection failed: " . $e->getMessage();
        }
    }
}
?>