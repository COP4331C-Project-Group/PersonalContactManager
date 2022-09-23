<?php
    require_once __DIR__ . '/../server/ServerException.php';

    class Database {
        private $hostname;
        private $username;
        private $password;
        private $dbName;
        private ?mysqli $mysql;

        public function __construct()
        {
            $this->hostname = getenv("HTTP_HOSTNAME");
            $this->username = getenv("HTTP_DATABASE_USERNAME");
            $this->password = getenv("HTTP_DATABASE_PASSWORD");
            $this->dbName = getenv("HTTP_DATABASE_NAME");

            $this->mysql = null;
        }
        
        /**
        * Connects to the database.
        * 
        * @return mysqli mysqli object upon successful connection.
        * @throws ServerException When connection to the database cannot be established.
        */
        public function connectToDatabase() : mysqli {        
            if (!is_null($this->mysql))
                return $this->mysql;

            try 
            {
                $mysql = new mysqli($this->hostname, $this->username, $this->password, $this->dbName);

                if ($mysql->connect_error !== null)
                    return false;

                return $mysql;
            } 
            catch (RuntimeException $e)
            {
                throw new ServerException("Cannot connect to the database.");
            }
        }
    }
?>
