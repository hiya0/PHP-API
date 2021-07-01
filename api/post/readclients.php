<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// include database and object files
include_once '../../config/database.php';
include_once '../../models/customer.php';

$database=new Database();
$db=$database->getConnection();

$customer=new Customer($db);
if(isset($_GET['userdate'])){
    $userdate = $_GET['userdate'];

    $res=$customer->read($userdate);
    if($res == NULL)
        exit;
    $rows=sqlsrv_has_rows( $res );
    if($rows){
        $cust_arr=array();
        $cust_arr["data"]=array();
        while($row=sqlsrv_fetch_array($res,SQLSRV_FETCH_ASSOC)){
            echo gettype($row['timestamp']);
            $cust_item=array(
                "client id" => $row['client_id'],
                "email" => $row['email'],
                "phone" => $row['phone'],
                "timestamp" => $row['timestamp']->format('Y-m-d H:i:s')
                    
            );
            array_push($cust_arr["data"],$cust_item);
        }
        http_response_code(200);
        echo json_encode($cust_arr);
    }
    else{
        http_response_code(404);
        echo json_encode(
            array("message" => "No clients")
        );
    }
}
else{
    http_response_code(404);
        echo json_encode(
            array("message" => "No input")
        );
}
