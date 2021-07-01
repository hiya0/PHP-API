<?php
class Database{
    private $serverName = "HSURFACE";
    private $username ="Hiya";
    private $password ="Hiya123$";
    private $database ="Client";
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