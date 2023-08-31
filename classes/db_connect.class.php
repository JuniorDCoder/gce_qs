<?php
require('../config/config.php');
class Database
{
    private static $instance = null;
    private $host = db_host;
    private $username = db_user;
    private $password = db_password;
    private $database_name = db_name;
    private static $conn;

    public function __construct(){
        self::$conn = new mysqli($this->host, $this->username, $this->password, $this->database_name);
        if (self::$conn->connect_error) {
            die("Connection Failed: ".self::$conn->connect_error);
        }
    }
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    public static function getConn() {
        return self::$conn;
    }
    public function insertId() {
        return self::$conn->insert_id;
    }

}
