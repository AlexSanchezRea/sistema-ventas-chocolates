<?php
class Database {
    private $host = "localhost";
    private $db_name = "sweetmett_db";
    private $username = "root";
    private $password = "";
    private static $conn = null;

    public function __construct() {
        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                    $this->username,
                    $this->password
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->exec("set names utf8");
            } catch(PDOException $e) {
                echo "Error de conexión: " . $e->getMessage();
            }
        }
    }

    public static function getConnection() {
        if (self::$conn === null) {
            new Database();
        }
        return self::$conn;
    }
}

// Función global para obtener la conexión
function getConnection() {
    return Database::getConnection();
}
?> 