<?php

header("Access-Control-Allow-Origin: *");
include("validate.php");

include("db_info.php");

$array_response = [];

// JWT VALIDATION

if(isset($_POST["token"]) ){

    $token = $_POST["token"];

    $validate= is_jwt_valid($_POST["token"] ,$secret = 'secret');

    if ($validate){ 
        $tokenParts = explode('.', $token);
        $payload = base64_decode($tokenParts[1]);
        $data = json_decode($payload, true);

        $user_id= $data["id"];

        //Get all users who are strangers to current user
        $query = $mysqli->prepare(
            "SELECT user_account.id,  user_account.fname,  user_account.lname
            FROM user_account 
            WHERE (user_account.id != ? AND user_account.id NOT IN(
                SELECT relationship.user1_id
                FROM relationship
                WHERE relationship.user2_id = ?) AND user_account.id NOT IN(
                SELECT relationship.user2_id
                FROM relationship
                WHERE relationship.user1_id = ?));");

        $query->bind_param("iii", $user_id, $user_id, $user_id);
        $query->execute();
        $array = $query->get_result();

        $array_response = [];
        while($friend_suggestion = $array->fetch_assoc()){
            $array_response[] = $friend_suggestion;
        }

        $json_response = json_encode($array_response);
        echo $json_response;
    }else{
        $array_response["error"] = "Invalid token.";
        $json_response = json_encode($array_response);
        echo $json_response;
        return false;
    }
}else{
    $array_response["error"] = "Token not received.";
    $json_response = json_encode($array_response);
    echo $json_response;
    return false;
};

$query->close();
$mysqli->close();

?>