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

        $user1_id= $data["id"];

        //Select first and last name of all who have sent current user a friend request
        $query = $mysqli->prepare("SELECT user_account.id, user_account.fname, user_account.lname
        FROM user_account
        JOIN relationship on relationship.user1_id = user_account.id
        WHERE relationship.user2_id = ? AND STATUS = 'pending';");
        $query->bind_param("i", $user1_id);
        $query->execute();
        $array = $query->get_result();

        $array_response = [];
        while($friend = $array->fetch_assoc()){
            $array_response[] = $friend;
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