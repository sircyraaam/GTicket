<?php
namespace app\Model\DbConnection;

require_once realpath(__DIR__ . '/../../../vendor/autoload.php');

use PDO;
use PDOException;
use Dotenv\Dotenv;

class DbConfig {

    private $db_server;
    private $db_port;
    private $db_name;
    private $db_username;
    private $db_password;
    private $apiKey;
    private $sdpUrl;
    public $dbConn = null;

    public function __construct() {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        $envPath = realpath(__DIR__ . '/../../../');
        $envFile = $envPath . '/.env';

        if ($envPath !== false && file_exists($envFile)) {
            $dotenv = Dotenv::createImmutable($envPath);
            $dotenv->load();
        } else {
            die("❌ Could not find .env file at: $envFile");
        }

        $this->db_server   = $_ENV['DB_SERVER'] ?? $_SERVER['DB_SERVER'] ?? 'localhost';
        $this->db_port     = $_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? '3306';
        $this->db_name     = $_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? '';
        $this->db_username = $_ENV['DB_USERNAME'] ?? $_SERVER['DB_USERNAME'] ?? '';
        $this->db_password = $_ENV['DB_PASSWORD'] ?? $_SERVER['DB_PASSWORD'] ?? '';
        $this->apiKey      = $_ENV['SDP_API_KEY'] ?? '';
        $this->sdpUrl      = $_ENV['SDP_URL'] ?? '';

        if (empty($this->db_name) || empty($this->db_username) || empty($this->db_password)) {
            die('❌ Missing database configuration in .env file.');
        }
    }

    public function db_connection() {
        try {
            $dsn = "mysql:host={$this->db_server};port={$this->db_port};dbname={$this->db_name};charset=utf8mb4";
            $this->dbConn = new PDO($dsn, $this->db_username, $this->db_password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
            ]);

            return $this->dbConn;

        } catch (PDOException $e) {
            die("❌ Connection failed: " . $e->getMessage());
        }
    }

    public function getApiKey() {
        return $this->apiKey;
    }

    public function getSdpUrl() {
        return $this->sdpUrl;
    }

    public function disconnect() {
        $this->dbConn = null;
    }
}
