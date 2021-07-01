<?php
// required headers
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// include database and object files
include_once '../../config/database.php';
include_once '../../models/customer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['number'])){
        $database=new Database();
        $db=$database->getConnection();

        $customer=new Customer($db);
        $number = $_POST['number'];
        //fetching client id from db
        $customer->findID($number);
    }
    else{
        http_response_code(404);
        echo json_encode(
        array("message" => "No ID")
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