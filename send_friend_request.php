<?php

include("db_info.php");

//GET $user id values

// SEND FRIEND REQUEST - CHANGE STATUS TO PENDING
function sendRequest ($user1_id, $user2_id) {
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
    }elseif($status == 'friend'){
        $array_response["error"] = "Users are already friends";
        return false;
    }elseif($status == 'pending'){
        $array_response["error"] = "There's already a pending friend request";
        return false;
    }

    $json_response = json_encode($array_response);
    echo $json_response;

    $query->close();
    $mysqli->close();
}
?>