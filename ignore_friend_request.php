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

    if(isset($_POST["friendID"])){
        $user2_id = $_POST["friendID"];

        // CHECK IF ALREADY PENDING FRIENDS
        $query = $mysqli->prepare("SELECT status FROM relationship WHERE ((user1_id = ? AND user2_id = ? AND status = 'pending') OR (user1_id = ? AND user2_id = ? AND status = 'pending'))");
        $query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
        $query->execute();
        $query->store_result();
        $num_rows = $query->num_rows;

        if($num_rows == 0){
          $array_response["error"] = "Invalid request.";
          $json_response = json_encode($array_response);
          return false;
        }else{
          // IGNORE FRIEND REQUEST - REMOVE PENDING ENTRY
          $query = $mysqli->prepare(
              "DELETE FROM relationship 
              WHERE (status='pending' AND 
              ((user1_id =? AND user2_id =?) OR (user1_id =? AND user2_id =?)))");

          $query->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
          $query->execute();

          $array_response["status"] = "Friend request ignored successfully.";
          $json_response = json_encode($array_response);
          echo $json_response;
        }
    }else{
      $array_response["error"] = "Missing friendID";
      $json_response = json_encode($array_response);
      echo $json_response;
      return false;
    }
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

