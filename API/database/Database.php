<?php
    /**
     * Connects to the database.
     * 
     * @return mysqli|false - mysqli object upon successful connection or false otherwise.
     */
    function connectToDatabaseOrFail() : mysqli|false {        
        $hostname = getenv("HTTP_HOSTNAME");
        $username = getenv("HTTP_DATABASE_USERNAME");
        $password = getenv("HTTP_DATABASE_PASSWORD");
        $dbName = getenv("HTTP_DATABASE_NAME");

        $mysql = new mysqli($hostname, $username, $password, $dbName);

        if ($mysql->connect_error != null)
            return false;

        return $mysql;
    }
?>