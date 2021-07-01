<?php
// required headers
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// include database and object files
include_once '../../config/database.php';
include_once '../../models/customer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['otp'])&& isset($_POST['phone'])){
        $database=new Database();
        $db=$database->getConnection();

        $customer=new Customer($db);
        $otp = $_POST['otp'];
        $phone=$_POST['phone'];
        //verifying otp entered
        $customer->verifyOTP($otp,$phone);
    }
    else{
        http_response_code(404);
        echo json_encode(
        array("message" => "No OTP or Number")
    );
    }
}
else{
    http_response_code(404);
    echo json_encode(
        array("message" => "Invalid request")
    );
}
?>