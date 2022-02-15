<?php

header("Access-Control-Allow-Origin: *");
include("db_info.php");

//GET $user id values
if(isset($_GET['user1']) && isset($_GET['user2'])){
    $user1_id =  $_GET["user1"];
    $user2_id =  $_GET["user2"];    
}

// ACCEPT FRIEND REQUEST - UPGRADE STATUS TO FRIENDS
global $mysqli;

// CHECK IF ALREADY FRIENDS
$query = $mysqli->prepare(
    "SELECT * 
    FROM relationship 
    WHERE (status = 'friend' AND ((user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)));");
$query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
$query->execute();
$query->store_result();
$num_rows = $query->num_rows;
$array_response = [];

if($num_rows == 0){
    $query = $mysqli->prepare(
        "UPDATE relationship SET status='friend' 
        WHERE (status='pending' AND 
        ((user1_id =? AND user2_id =?) OR (user1_id =? AND user2_id =?)));");
    
    $query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
    $query->execute();
    $query->store_result();
    $array_response["status"] = "Friend request accepted successfully.";
    $json_response = json_encode($array_response);
    echo $json_response;

}else{
    $array_response["error"] = "Users are already friends";
    $json_response = json_encode($array_response);
    echo $json_response;

    return false;
}

$query->close();
$mysqli->close();

?>
