<?php

include("db_info.php");


if(isset($_POST["name"])){
    $name = $_POST["name"];
}else{
    die("Please enter a name");
};    

if(isset($_POST["lastname"])){
    $last_name = $_POST["lastname"];
}else{
    die("Please enter a last name");
};    

if(isset($_POST["birthdate"])){
    $birth_date = $_POST["birthdate"];
}else{
    die("Please enter a birth date");
};    

if(isset($_POST["email"])){
    $email = $_POST["email"];
    $query = $mysqli->prepare("SELECT id FROM user_account WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();

    $query->store_result();
    $num_rows = $query->num_rows;
    if($num_rows != 0){
        $array_response["status"] = "Email already exists! Please use another email.";
        $json_response = json_encode($array_response);
        die($json_response);
    }
}else{
    die("Please enter an email");
};   

if(isset($_POST["password"])){
    $password = $_POST["password"];
    $password = hash("sha256", $password);
}else{
    die("Please enter a password");
}; 


$query = $mysqli->prepare("INSERT INTO user_account (fname, lname, bday, email, password) VALUES (?, ?, ?, ?, ?)"); 
$query->bind_param("sssss", $name, $last_name, $birth_date, $email, $password);
$query->execute();

$array_response = [];
$array_response["status"] = "Signed up successfully";
$json_response = json_encode($array_response);
echo $json_response;





$query->close();
$mysqli->close();
?>