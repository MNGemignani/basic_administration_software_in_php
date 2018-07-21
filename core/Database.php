<?php
/**
 * Created by PhpStorm.
 * User: Mateus
 * Date: 11/01/2018
 * Time: 14:12
 */

class Database
{
    private $host = "localhost";
    private $db_name = "barao";
    private $username = "root";
    private $password = "";
    public $conn;

    public function dbConnection()
    {

        $this->conn = null;
        try
        {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $exception)
        {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }

}