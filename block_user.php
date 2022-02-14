
<?php

header("Access-Control-Allow-Origin: *");

include("db_info.php");

//GET $user id values
$user1_id =  $_GET[$user1_id];
$user2_id = $_GET[$user2_id];

// BLOCK - ADD RELATIONSHIP ENTRY AS BLOCKED
$array_response = [];
global $mysqli;
$query = $mysqli->prepare("INSERT INTO relationship (user1_id, user2_id, status) VALUES (?,?,'blocked')");

$query->bind_param("ii", $user1_id, $user2_id);
$query->execute();

$array_response["status"] = "Friend blocked successfully.";
$json_response = json_encode($array_response);


$query->close();
$mysqli->close();

?>
