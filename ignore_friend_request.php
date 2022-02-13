<?php

include("db_info.php");

// IGNORE FRIEND REQUEST - REMOVE PENDING ENTRY
  function ignoreRequest ($user1_id, $user2_id) {
    global $mysqli;
    $query = $mysqli->prepare(
        "DELETE FROM relationship 
        WHERE (status='pending' AND 
        ((user1_id =? AND user2_id =?) OR (user1_id =? AND user2_id =?)))");

    $query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
    $query->execute();
    
    $query->close();
    $mysqli->close();
}
?>
