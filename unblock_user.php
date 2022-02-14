<?php

header("Access-Control-Allow-Origin: *");
include("db_info.php");

//GET $user id values
$user1_id =  $_GET[$user1_id];
$user2_id = $_GET[$user2_id];

// CHECK IF ALREADY FRIENDS
$query = $mysqli->prepare("SELECT status FROM relationship WHERE ((user1_id = ? AND user2_id = ? AND status = 'blocked') OR (user1_id = ? AND user2_id = ? AND status = 'blocked'))");
$query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
$query->execute();
$query->store_result();
$num_rows = $query->num_rows;

$array_response = [];

if($num_rows == 0){
  $array_response["error"] = "Invalid request.";
  $json_response = json_encode($array_response);

  return false;
}else{
    // UNBLOCK - DELETE BLOCKED RELATIONSHIP ENTRY
    global $mysqli;
    $query = $mysqli->prepare(
        "DELETE FROM relationship 
        WHERE ((user1_id =? AND user2_id =? AND status='blocked') OR 
        (user1_id =? AND user2_id =? AND status='blocked'))");

    $query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
    $query->execute();

    $array_response["status"] = "Friend unblocked successfully.";
    $json_response = json_encode($array_response);

    $query->close();
    $mysqli->close();
}
?>
