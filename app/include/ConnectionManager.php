<?php

class ConnectionManager
{

    public function getConnection()
    {

        $host = "localhost";
        $username = "root";
        $password = "";

        $dbname = "project-g8t4";
        $port = 3306;

        if (strpos(php_uname(), "Linux ip-172-31-23-67 4.4.0") === 0) {
            $password =  'px90cz2eV8Yb';
            $port = 22;
        }
        $url  = "mysql:host={$host};dbname={$dbname};port={$port}";

        $conn = new PDO($url, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
}