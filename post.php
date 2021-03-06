<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
include("validate.php");

// EDIT POST
function f_put($token, $post_id) {
    include ("db_info.php");
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
     
// MAKE A POST
function f_post($token) {
    include ("db_info.php");
    $post=$_POST["post"];
    $tokenParts = explode('.', $token);
    $payload = base64_decode($tokenParts[1]);
    $data = json_decode($payload, true);
    $user_id=$data["id"];
    $date= date("Y-m-d H:i:s");
    
    $query = $mysqli->prepare("INSERT INTO user_post (account_id, post_text, post_date) VALUES(?,?,?)");
    $query->bind_param("iss", $user_id, $post, $date);
    $query->execute();
    
    $array_response = [];
    $array_response["status"] = "You've just posted!";
    $array_response["date"] = $date;
    echo json_encode($array_response);
}
    
// DELETE A POST
function f_delete($token, $post_id) {
    include ("db_info.php");
    $tokenParts = explode('.', $token);
    $payload = base64_decode($tokenParts[1]);
    $data = json_decode($payload, true);
    
    $query = $mysqli->prepare("DELETE FROM user_post where id=?");
    $query->bind_param("i", $post_id);
    $query->execute();
    $array_response["status"] = "You've just deleted post!";
    echo json_encode($array_response);
}
    
// GET ALL POSTS
function get_Allposts($token){
    include ("db_info.php");
    $tokenParts = explode('.', $token);
    $payload = base64_decode($tokenParts[1]);
    $data = json_decode($payload, true);
    $user_id= $data["id"]; 
    $query = $mysqli->prepare(
        "SELECT DISTINCT user_post.id as post_id, user_post.post_text, user_post.post_date, user_account.fname, user_account.lname , user_account.id
        FROM  user_post
        JOIN user_account on user_post.account_id = user_account.id
        JOIN relationship on relationship.user1_id = user_account.id
            WHERE (user_account.id = ?
                OR user_account.id IN(
                SELECT relationship.user1_id
                FROM relationship
                WHERE relationship.user2_id = ?) 
                OR user_account.id IN(
                SELECT relationship.user2_id
                FROM relationship
                WHERE relationship.user1_id = ?))
                Order By user_post.post_date;");
        $query->bind_param("iii", $user_id, $user_id, $user_id);

    $query->execute();
    $array=$query->get_result();
    $array_response=[];
    while($row=$array->fetch_assoc()){
        $array_response[]=$row;
    }
    $json_response = json_encode($array_response);
    echo $json_response;
}

// JWT VALIDATION

if(isset($_POST["token"]) ){

        $token = $_POST["token"];

        $validate= is_jwt_valid($_POST["token"] ,$secret = 'secret');
    
        if ($validate){ 

            if ($_POST['function'] == 'POST'){
                f_post($token);
                    
            }else if ($_POST['function'] == 'PUT' && isset($_POST["post_id"])) {
                $post_id=$_POST["post_id"];
                f_put($token,$post_id);
                    
            }else if($_POST['function'] == 'DELETE' && isset($_POST["post_id"])){
                $post_id=$_POST["post_id"];
                f_delete($token, $post_id) ;            
            }else if($_POST['function']=='GET'){
                get_Allposts($token);
            }else {
                $array_response["error"] = "Send function method or correct post_id";
                $json_response = json_encode($array_response);
                echo $json_response;
                return false;
            } 
        } else {
            $array_response["error"] = "Invalid token.";
            $json_response = json_encode($array_response);
            echo $json_response;
            return false;
        }
    }else{
        $array_response["error"] = "Missing token.";
        $json_response = json_encode($array_response);
        echo $json_response;
        return false;
    };
?>