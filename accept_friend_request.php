<?php

header("Access-Control-Allow-Origin: *");
include("db_info.php");

//GET $user id values
$user1_id =  $_GET[$user1_id];
$user2_id = $_GET[$user2_id];

// ACCEPT FRIEND REQUEST - UPGRADE STATUS TO FRIENDS
global $mysqli;

// CHECK IF ALREADY FRIENDS
$query = $mysqli->prepare("SELECT status FROM relationship WHERE ((user1_id = ? AND user2_id = ? AND status = 'friend') OR (user1_id = ? AND user2_id = ? AND status = 'friend'))");
$query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
$query->execute();
$query->store_result();
$num_rows = $query->num_rows;

$array_response = [];

if($num_rows == 0){
    $query = $mysqli->prepare(
        "UPDATE relationship SET status='friend' 
        WHERE (status='pending' AND 
        ((user1_id =? AND user2_id =?) OR (user1_id =? AND user2_id =?)))");
    
    $query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
    $query->execute();
    $query->store_result();
    $array_response["status"] = "Friend request accepted successfully.";
    $json_response = json_encode($array_response);

}elseif($status == 'friend'){
    $array_response["error"] = "Users are already friends";
    $json_response = json_encode($array_response);

    return false;
}

$query->close();
$mysqli->close();

?>
