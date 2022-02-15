<?php

header("Access-Control-Allow-Origin: *");

include("db_info.php");

//GET $user id value
$user_id =  $_GET["user_id"];

// //Get all users who are strangers to current user
$query = $mysqli->prepare(
    "SELECT user_account.id,  user_account.fname,  user_account.lname
    FROM user_account 
    WHERE (user_account.id != ? AND user_account.id NOT IN(
          SELECT relationship.user1_id
          FROM relationship
          WHERE relationship.user2_id = ?) AND user_account.id NOT IN(
          SELECT relationship.user2_id
          FROM relationship
          WHERE relationship.user1_id = ?));");

$query->bind_param("iii", $user_id, $user_id, $user_id);
$query->execute();
$array = $query->get_result();

$array_response = [];
while($friend_suggestion = $array->fetch_assoc()){
    $array_response[] = $friend_suggestion;
}

$json_response = json_encode($array_response);
echo $json_response;
?>