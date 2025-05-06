<?php
require_once('config.php');
class DataBase
{
    private $con;

    function __construct()
    {
        $this->con = $this->connect();
    }

    private function connect()
    {
        $string = "mysql:host=localhost;dbname=mychat_db";

        try {
            $connection  = new PDO($string, DBUSER, DBPASS);
            return $connection;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die;
        }

        return false;
    }
    //write to database
    public function write($query, $data_array = [])
    {
        $con = $this->connect();
        $statment = $con->prepare($query);
        $chalk = $statment->execute($data_array);
        if ($chalk) {
            return true;
        }
        return false;
    }
    //read from database
    public function read($query, $data_array = [])
    {
        $con = $this->connect();
        $statment = $con->prepare($query);
        $chalk = $statment->execute($data_array);
        if ($chalk) {
            $result = $statment->fetchAll(PDO::FETCH_OBJ);
            if (is_array($result) && count($result) > 0) {
                return $result;
            }

            return false;
        }
        return false;
    }
    public function get_user($userid)
    {
        $con = $this->connect();
        $arr['userid'] = $userid;
        $query = "SELECT * FROM users WHERE user_id = :userid limit 1";
        $statment = $con->prepare($query);
        $chalk = $statment->execute($arr);
        if ($chalk) {
            $result = $statment->fetchAll(PDO::FETCH_OBJ);
            if (is_array($result) && count($result) > 0) {
                return $result[0];
            }

            return false;
        }
        return false;
    }

    public function generate_id($max)
    {
        $rand = "";
        $rand_count = rand(4, $max);
        for ($i = 0; $i < $rand_count; $i++) {
            $r = rand(0, 9);
            $rand .= $r;
        }

        return $rand;
    }
}
