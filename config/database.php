<?php
class Database{
    private $serverName = "serverName";
    private $username ="username";
    private $password ="password";
    private $database ="DBname";
    public $conn;
  
    // get the database connection
    public function getConnection(){
  
        $this->conn = null;
  
        try{
            $connInfo = array(
                "UID" => $this->username,
                "PWD" => $this->password,
                "Database" => $this->database
            );
            $this->conn = sqlsrv_connect($this->serverName, $connInfo);
        }catch(PDOException $e){
            echo "Connection error: " . $e->getMessage();
        }
  
        return $this->conn;
    }
}
?>