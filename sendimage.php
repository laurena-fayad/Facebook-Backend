<?php

include("db_info.php");
$query = $mysqli->prepare("SELECT name FROM images");

$query->execute();

$query->store_result();
$num_rows = $query->num_rows;
$query->bind_result($name);
$query->fetch();

echo json_encode(array("url"=>$name));
?>