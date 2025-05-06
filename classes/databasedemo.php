<?php

// namespace demo;

use Exception;

class DataBase{
    private $con;

    function __construct(){
        $this->con=$this->connect();
    }

    private function connect(){
        $db_name="mychat_db";
        $host="localhost";
        $user="root";
        $password = "";
        $connection="";
        try{
            $connection = mysqli_connect($host,$user,$password,$db_name);
            return $connection;
        }catch(Exception $e){
            echo $e-> getMessage();
            die;
        }
        return false;
    }
    public function write($query,$data_array=[]){
        
    }
}

