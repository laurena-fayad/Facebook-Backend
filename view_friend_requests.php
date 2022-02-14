<?php

header("Access-Control-Allow-Origin: *");

include("db_info.php");

//GET $user id value
$user_id =  $_GET["user_id"];

// //Select first and last name of all who have sent current user a friend request
$query = $mysqli->prepare("SELECT user_account.fname, user_account.lname
FROM user_account
JOIN relationship on relationship.user1_id = user_account.id
WHERE relationship.user2_id = ? AND STATUS = 'pending';");
$query->bind_param("i", $user_id);
$query->execute();
$array = $query->get_result();

$array_response = [];
while($friend = $array->fetch_assoc()){
    $array_response[] = $friend;
}

$json_response = json_encode($array_response);
echo $json_response;
?>