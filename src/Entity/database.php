<?php
class Db
{
    private $instance = null;
    private PDO $conn;

    private function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "bank";

        try {
            $this->conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static getinstance(){
        if(self::$instance === null){
            self::$instance = new Db;
        }
        return self::$instance;
    }

    public function connection(): PDO
    {
        echo "connexion<br>";
        return $this->conn;
    }
}
?>