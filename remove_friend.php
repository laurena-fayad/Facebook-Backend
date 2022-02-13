<?php

include("db_info.php");

//REMOVE FRIEND - DELETE RELATIONSHIP ENTRY
function removeFriend ($user1_id, $user2_id) {
    global $mysqli;
    $query = $mysqli->prepare(
        "DELETE FROM relationship 
        WHERE ((user1_id =? AND user2_id =? AND status='friend') OR 
        (user1_id =? AND user2_id =? AND status='friend'))");

    $query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
    $query->execute();

    $query->close();
    $mysqli->close();
}
?>