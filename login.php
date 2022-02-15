<?php
header("Access-Control-Allow-Origin: *");

include("db_info.php");
include ("jwt.php");

if(isset($_POST["email"])){
    $email = $mysqli->real_escape_string($_POST["email"]);
}else{
    
    die("Please enter an email");
}

if(isset($_POST["password"])){
    $password = $mysqli->real_escape_string($_POST["password"]);
    $password = hash("sha256", $password);
}else{
    die("Please enter a password");
}

$query = $mysqli->prepare("SELECT id FROM user_account WHERE email = ? AND password = ?");
$query->bind_param("ss", $email, $password);
$query->execute();

$query->store_result();
$num_rows = $query->num_rows;
$query->bind_result($id);
$query->fetch();

$array_response = [];

if($num_rows == 0){
    $array_response["status"] = "User not found!";
}else{
    $array_response["status"] = "Logged In !";
    
    $token=createJwt($id);
    $array_response["token"] = $token;
}
echo json_encode($array_response);
 



$query->close();
$mysqli->close();

?>
