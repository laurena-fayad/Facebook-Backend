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
        
        // VIEW Friends 
        $query = $mysqli->prepare(
            "SELECT DISTINCT user_account.id, user_account.fname, user_account.lname
            FROM user_account
            INNER JOIN relationship AS relationship1 ON user_account.id = relationship1.user1_id OR user_account.id = relationship1.user2_id
            INNER JOIN relationship AS relationship2 ON user_account.id = relationship2.user2_id OR user_account.id = relationship2.user1_id            WHERE (user_account.id IN (SELECT relationship.user1_id
                                        FROM relationship
                                        WHERE (relationship.user2_id = ? AND relationship.status = 'friend')) 
                OR user_account.id IN (SELECT relationship.user2_id
                                        FROM relationship
                                        WHERE (relationship.user1_id = ? AND relationship.status = 'friend')));");
        $query->bind_param("ii", $user_id, $user_id);
        $query->execute();
        $array = $query->get_result();

        while($friend = $array->fetch_assoc()){
            $array_response[] = $friend;
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