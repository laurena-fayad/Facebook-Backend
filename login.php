<?php
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Headers:*");

include("db_info.php");
include ("jwt.php");

$array_response = [];

if(isset($_POST["email"])){
    $email = $mysqli->real_escape_string($_POST["email"]);
}else{
    $array_response["status"] = "Please enter an email.";
    // echo json_encode($array_response);
    $json_response= json_encode($array_response);
    die($json_response);
}

if(isset($_POST["password"])){
    $password = $mysqli->real_escape_string($_POST["password"]);
    $password = hash("sha256", $password);
}else{
    $array_response["status"] = "Please enter a password.";
    $json_response= json_encode($array_response);
    die($json_response);
}

$query = $mysqli->prepare("SELECT id, fname, lname FROM user_account WHERE email = ? AND password = ?");
$query->bind_param("ss", $email, $password);
$query->execute();

$query->store_result();
$num_rows = $query->num_rows;
$query->bind_result($id, $fname, $lname);
$query->fetch();


if($num_rows == 0){
    $array_response["status"] = "User not found!";
}else{
    $array_response["status"] = "Logged In !";
    
    $token=createJwt($id);
    $array_response["token"] = $token;
    $array_response["fname"] = $fname;
    $array_response["lname"] = $lname;
}
echo json_encode($array_response);

$query->close();
$mysqli->close();

?>
