<?php

include("db_info.php");

// SEND FRIEND REQUEST - CHANGE STATUS TO PENDING
function sendRequest ($user1_id, $user2_id) {
    // CHECK IF ALREADY FRIENDS/PENDING
    $query = $mysqli->prepare(
        "SELECT status 
        FROM relationship 
        WHERE ((user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)");
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
        return = false;
    }elseif($status == 'pending'){
        $array_response["error"] = "There's already a pending friend request";
        return = false;
    }

    $json_response = json_encode($array_response);
    echo $json_response;
}

// ACCEPT FRIEND REQUEST - UPGRADE STATUS TO FRIENDS
function acceptRequest ($user1_id, $user2_id) {
    $query = $mysqli->prepare(
        "UPDATE relationship SET status='friend' 
        WHERE (status='pending' AND 
        ((user1_id =? AND user2_id =?) OR (user1_id =? AND user2_id =?)))");

    $query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
    $query->execute();
    $query->store_result();
}

// IGNORE FRIEND REQUEST - REMOVE PENDING ENTRY
  function ignoreRequest ($user1_id, $user2_id) {
    $query = $mysqli->prepare(
        "DELETE FROM relationship 
        WHERE (status='pending' AND 
        ((user1_id =? AND user2_id =?) OR (user1_id =? AND user2_id =?)))");

    $query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
    $query->execute();
}

//REMOVE FRIEND - DELETE RELATIONSHIP ENTRY
function removeFriend ($user1_id, $user2_id) {
    $query = $mysqli->prepare(
        "DELETE FROM relationship 
        WHERE ((user1_id =? AND user2_id =? AND status='friend') OR 
        (user1_id =? AND user2_id =? AND status='friend'))");

    $query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
    $query->execute();
}

// BLOCK - ADD RELATIONSHIP ENTRY AS BLOCKED
function block ($user1_id, $user2_id) {
    $query = $mysqli->prepare("INSERT INTO relationship (user1_id, user2_id, status) VALUES (?,?,'blocked')");

    $query->bind_param("ii", $user1_id, $user2_id);
    $query->execute();
}

// UNBLOCK - DELETE BLOCKED RELATIONSHIP ENTRY
function unblock ($user1_id, $user2_id) {
    $query = $mysqli->prepare(
        "DELETE FROM relationship 
        WHERE ((user1_id =? AND user2_id =? AND status='blocked') OR 
        (user1_id =? AND user2_id =? AND status='blocked'))");

    $query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
    $query->execute();
}

$query->close();
$mysqli->close();

?>