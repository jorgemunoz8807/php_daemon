<?php

/**
 * Description of db_connect
 *
 * @author jmunoz
 */
class db_connect_sample {

    var $host = '';
    var $username = '';
    var $password = '';
    var $database = '';

    function __construct() {
        
    }

    function DB_Connection() {
//        $config = new config();
        $conn = new mysqli($this->host, $this->username, $this->password, $this->database);

        // Check connection RDS 
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }

    function closeConnection() {
        mysql_close($this->myconn);

        echo "Connection closed";
    }

}

?>