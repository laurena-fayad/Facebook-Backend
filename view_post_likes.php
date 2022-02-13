<?php

include("db_info.php");
// VIEW LIKES 
global $mysqli;
$query = $mysqli->prepare("SELECT COUNT(account_id) FROM post_like WHERE post_id = ?");
$query->bind_param("i", $post_id);
$query->execute();
$query->store_result();
$query->bind_result($nb_of_likes);
$query->fetch();

$array_response["nb_of_likes"] = $nb_of_likes;

$json_response = json_encode($array_response);
echo $json_response;

$query->close();
$mysqli->close();
?>