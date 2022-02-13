<?php

include("db_info.php");

// LIKE POST - ADD ENTRY TO POST_LIKE TABLE
function likePost ($user_id, $post_id) {
    global $mysqli;
    $query = $mysqli->prepare("INSERT INTO post_like (post_id, account_id) VALUES (?,?)");

    $query->bind_param("ii", $post_id, $user_id);
    $query->execute();

    $query->close();
    $mysqli->close();
}
?>

