<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// EDIT POST
    function f_put($token, $post_id) {
        include("db_info.php");
        $post=$_POST["post"];
        $tokenParts = explode('.', $token);
        $payload = base64_decode($tokenParts[1]);
        $data = json_decode($payload, true);
        $user_id=$data["id"];
        $date= date("Y-m-d H:i:s");
        $query=$mysqli->prepare("UPDATE user_post SET post_text=? WHERE account_id=? AND id=?");
        $query->bind_param("sii", $post, $user_id, $post_id);
        $query->execute();
        $array_response=[];
        $array_response["status"] = "You've just edited a post";
        $array_response["date"] = $date;
        echo json_encode($array_response);
    };
     


?>