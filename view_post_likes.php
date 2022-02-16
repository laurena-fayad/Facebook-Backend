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
        
        if(isset($_POST["post_id"])){
            $post_id = $_POST["post_id"];

            // VIEW LIKES 
            $query = $mysqli->prepare("SELECT COUNT(account_id) FROM post_like WHERE post_id = ?");
            $query->bind_param("i", $post_id);
            $query->execute();
            $query->store_result();
            $num_rows = $query->num_rows;
            $query->bind_result($nb_of_likes);
            $query->fetch();

            if($num_rows == 0){
                $array_response["likes"] = "0";
                $json_response = json_encode($array_response);
                echo $json_response;
            }else{
                $array_response["likes"] = $nb_of_likes;
                $json_response = json_encode($array_response);
                echo $json_response;
            }
        }else{
            $array_response["error"] = "Post ID not found";
            $json_response = json_encode($array_response);
            return false;    
        }
    }else{
        $array_response["error"] = "Invalid token.";
        $json_response = json_encode($array_response);
        return false;
    }
}else{
    $array_response["error"] = "Token not received.";
    $json_response = json_encode($array_response);
    return false;
};

$query->close();
$mysqli->close();
?>
