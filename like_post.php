<?php

include("db_info.php");

// LIKE POST - ADD ENTRY TO POST_LIKE TABLE
global $mysqli;
$query = $mysqli->prepare("SELECT * FROM post_like WHERE (post_id = ? AND account_id = ?)");
$query->bind_param("ii", $post_id, $user_id);
$query->execute();
$query->store_result();
$num_rows = $query->num_rows;

$array_response = [];

if($num_rows != 0){
    $array_response["error"] = "You already liked this post.";
    $json_response = json_encode($array_response);
    echo $json_response;

    return false;
}

$query = $mysqli->prepare("INSERT INTO post_like (post_id, account_id) VALUES (?,?)");
$query->bind_param("ii", $post_id, $user_id);
$query->execute();

$query->close();
$mysqli->close();
?>