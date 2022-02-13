<?php

include("db_info.php");

// LIKE POST - ADD ENTRY TO POST_LIKE TABLE
function LikePost ($user1_id, $user2_id) {
    global $mysqli;
    $query = $mysqli->prepare(
        "UPDATE relationship SET status='friend' 
        WHERE (status='pending' AND 
        ((user1_id =? AND user2_id =?) OR (user1_id =? AND user2_id =?)))");

    $query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
    $query->execute();
    $query->store_result();

    $query->close();
    $mysqli->close();
}
?>