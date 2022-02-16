<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
include("db_info.php");


if(isset($_POST["name"]) && !empty($_POST["name"])){ 
    $name = $_POST["name"];
}else{
    
    $array_response["status"] = "PLEASE ENTER NAME";
    $json_response = json_encode($array_response);
    die($json_response);
};    

if(isset($_POST["lastname"])){ 
    $last_name = $_POST["lastname"];
}else{
    $array_response["status"] = "PLEASE ENTER LAST NAME";
        $json_response = json_encode($array_response);
        echo($json_response);
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
    $array_response["status"] = "PLEASE ENTER EMAIL ";
        $json_response = json_encode($array_response);
        die($json_response);
};   

if(isset($_POST["password"])){
    $password = $_POST["password"];
    $password = hash("sha256", $password);
}else{
    $array_response["status"] = "PLEASE ENTER PASSWORD";
        $json_response = json_encode($array_response);
        die($json_response);
}; 


$query = $mysqli->prepare("INSERT INTO user_account (fname, lname, email, password) VALUES(?,?,?,?)"); 
$query->bind_param("ssss", $name, $last_name,$email, $password);
$query->execute();

$array_response = [];
$array_response["status"] = "SIGNED UP SUCCESSFULLY";
$json_response = json_encode($array_response);
echo $json_response;





$query->close();
$mysqli->close();
?>