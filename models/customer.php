<?php

class Customer{
  
    // database connection and table name
    private $conn;
    private $table_name = 'Customer';
  
    // object properties
    public $client_id;
    public $email;
    public $phone;
    public $otp;
    public $timestamp;

    public function __construct($db){
        $this->conn = $db;
    }

    //send OTP
    private function sendOTP($phone, $otp){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2?authorization=authToken&sender_id=TXTIND&message=".urlencode('OTP:'.$otp)."&route=v3&numbers=".urlencode($phone),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache"
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err){
        http_response_code(404);
        echo json_encode(
            array("message" => "SMS not sent")
        );
        return;
    }
    http_response_code(200);
    echo json_encode(
        array("message" => "OTP sent")
    );
    }   

    //fetch phone from db 
    public function findPh($id){
        $q2 = "SELECT phone FROM ".$this->table_name." WHERE client_id ='".$id."';";
        $getPh =sqlsrv_prepare($this->conn,$q2);
        sqlsrv_execute($getPh);
        $phone = sqlsrv_fetch($getPh);
        $phone=sqlsrv_get_field($getPh,0);
        if($phone== NULL){
            http_response_code(404);
            echo json_encode(
                array("message" => "Phone number does not exist")
            );
            return;
        }
        $otp = mt_rand(10000,99999);
        //adding otp in db
        $u = "UPDATE ".$this->table_name." SET otp =".$otp." WHERE phone='".$phone."';";
        $updateOTP = sqlsrv_prepare($this->conn,$u);
        sqlsrv_execute($updateOTP);
        $this->sendOTP($phone,$otp);
    }

    //verify ID
    public function findID($id){
        $q1 = "SELECT client_id FROM ".$this->table_name." WHERE client_id ='".$id."';";
        $getID=sqlsrv_prepare($this->conn,$q1);
        sqlsrv_execute($getID);
        $cID = sqlsrv_fetch($getID);
        $cID=sqlsrv_get_field($getID,0);
        if($cID == NULL){
            http_response_code(404);
            echo json_encode(
                array("message" => "Client ID does not exist")
            );
            return;
        }
        $this->findPh($cID);
    }

    //verifyOTP
    public function verifyOTP($userotp,$phone){
        $q = "SELECT otp FROM ".$this->table_name." WHERE phone ='".$phone."';";
        $getOtp = sqlsrv_prepare($this->conn,$q);
        sqlsrv_execute($getOtp);
        $otp = sqlsrv_fetch($getOtp);
        $otp=sqlsrv_get_field($getOtp,0);
        if($otp== NULL){
            http_response_code(404);
            echo json_encode(
                array("message" => "OTP does not exist")
            );
            return;
        }
        //checking if otp has been entered by the user
        if($otp == $userotp)
            {
                date_default_timezone_set('Asia/Kolkata');
                $d=date('Y-m-d H:i:s');
                $q3="UPDATE ".$this->table_name." SET timestamp='".$d."' WHERE phone ='".$phone."';";
                $setDate=sqlsrv_prepare($this->conn,$q3);
                sqlsrv_execute($setDate);
                http_response_code(200);
                echo json_encode(
                    array("message" => "Timestamp Updated")
                );
            }
        else{
            http_response_code(404);
            echo json_encode(
                array("message" => "Incorrect OTP")
            );
        }
    }


    //get registered clients
    public function read($userdate){
        $today=date('Y-m-d H:i:s');
        if($userdate > $today)
        {
            http_response_code(404);
            echo json_encode(
                array("message" => "Invalid Date.")
            );
            return;
        }
        else {
            $query="SELECT * from ".$this->table_name." WHERE timestamp BETWEEN '".$userdate." 00: 00: 01' and '".$userdate." 23: 59: 59';";
            $res=sqlsrv_prepare($this->conn,$query);
            sqlsrv_execute($res);
            return $res;
        }
    }

}
?>