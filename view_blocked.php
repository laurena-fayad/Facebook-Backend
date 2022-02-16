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

        // VIEW BLOCKED
        $query = $mysqli->prepare(
            "SELECT relationship.user2_id, user_account.fname, user_account.lname
            FROM relationship
            join user_account on relationship.user2_id = user_account.id
            WHERE relationship.user1_id = ? AND relationship.status = 'blocked';");

        $query->bind_param("i", $user1_id);
        $query->execute();

        $array = $query->get_result();

        while($blocked = $array->fetch_assoc()){
            $array_response[] = $blocked;
            $json_response = json_encode($array_response);
        }
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