<?php

header("Access-Control-Allow-Origin: *");

include("db_info.php");

//GET post id value
$post_id = $_GET[$post_id];

// VIEW LIKES 
global $mysqli;
$query = $mysqli->prepare("SELECT COUNT(account_id) FROM post_like WHERE post_id = ?");
$query->bind_param("i", $post_id);
$query->execute();
$query->store_result();
$num_rows = $query->num_rows;
$query->bind_result($nb_of_likes);
$query->fetch();

$array_response = [];

if($num_rows == 0){
    $array_response["nb_of_likes"] = "0";
    $json_response = json_encode($array_response);

    return false;
}

$array_response["nb_of_likes"] = $nb_of_likes;
$json_response = json_encode($array_response);

$query->close();
$mysqli->close();
?>
