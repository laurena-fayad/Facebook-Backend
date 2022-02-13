<?php

include("db_info.php");

// BLOCK - ADD RELATIONSHIP ENTRY AS BLOCKED
function block ($user1_id, $user2_id) {
    global $mysqli;
    $query = $mysqli->prepare("INSERT INTO relationship (user1_id, user2_id, status) VALUES (?,?,'blocked')");

    $query->bind_param("ii", $user1_id, $user2_id);
    $query->execute();

    $query->close();
    $mysqli->close();
}
?>