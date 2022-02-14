<?php

header("Access-Control-Allow-Origin: *");
include("db_info.php");

//GET $user id values
$user1_id =  $_GET[$user1_id];
$user2_id = $_GET[$user2_id];

// SEND FRIEND REQUEST - CHANGE STATUS TO PENDING

global $mysqli;
// CHECK IF ALREADY FRIENDS/PENDING
$query = $mysqli->prepare("SELECT status FROM relationship WHERE ((user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?))");
$query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
$query->execute();
$query->store_result();
$num_rows = $query->num_rows;
$query->bind_result($status);
$query->fetch();

$array_response = [];

if($num_rows == 0){
    $query = $mysqli->prepare("INSERT INTO relationship (user1_id, user2_id, status) VALUES (?,?,'pending')");
    $query->bind_param("ii", $user1_id, $user2_id);
    $query->execute();
    $array_response["status"] = "Friend request sent successfully.";
    $json_response = json_encode($array_response);

}elseif($status == 'friend'){
    $array_response["error"] = "Users are already friends.";
    $json_response = json_encode($array_response);
    return false;
}elseif($status == 'pending'){
    $array_response["error"] = "There's already a pending friend request.";
    $json_response = json_encode($array_response);
    return false;
}

$query->close();
$mysqli->close();

?>
