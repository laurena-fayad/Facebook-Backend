<?php

include("db_info.php");
include("validate.php");
header("Access-Control-Allow-Origin: *");


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

            // LIKE POST - ADD/REMOVE ENTRY TO POST_LIKE TABLE
            $query = $mysqli->prepare("SELECT * FROM post_like WHERE (post_id = ? AND account_id = ?)");
            $query->bind_param("ii", $post_id, $user_id);
            $query->execute();
            $query->store_result();
            $num_rows = $query->num_rows;

            if($num_rows != 0){
                $query1 = $mysqli->prepare("DELETE FROM post_like WHERE (post_id = ? AND account_id = ?)");
                $query1->bind_param("ii", $post_id, $user_id);
                $query1->execute();

                $query2 = $mysqli->prepare("SELECT COUNT(account_id) FROM post_like WHERE post_id = ? GROUP BY post_id;");
                $query2->bind_param("i", $post_id);
                $query2->execute();

                $num_rows2 = $query2->num_rows;
                $query2->bind_result($likes);
                $query2->fetch();

                if($num_rows2 == 0){
                    $array_response["status"] = "Post unliked";
                    $array_response["likes"] = 0;
                     $json_response = json_encode($array_response);
                    echo $json_response;
                }else{
                    $array_response["status"] = "Post unliked";
                    $array_response["likes"] = $likes;
                    $json_response = json_encode($array_response);
                    echo $json_response;
                }
            }else{
                $query1 = $mysqli->prepare("INSERT INTO post_like (post_id, account_id) VALUES (?,?)");
                $query1->bind_param("ii", $post_id, $user_id);
                $query1->execute();

                $query2 = $mysqli->prepare("SELECT COUNT(account_id) FROM post_like WHERE post_id = ? GROUP BY post_id;");
                $query2->bind_param("i", $post_id);
                $query2->execute();

                $num_rows2 = $query2->num_rows;
                $query2->bind_result($likes);
                $query2->fetch();

                $array_response["status"] = "Post liked";
                $array_response["likes"] = $likes;
                $json_response = json_encode($array_response);
                echo $json_response;
            }
        }else{
            $array_response["error"] = "Post ID not found";
            $json_response = json_encode($array_response);
            return false;    
        }
    }else {
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
