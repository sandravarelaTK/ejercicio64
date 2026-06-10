<?php

include 'db.php';

$name =  $_POST["name"];

$email= $_POST["email"];

$sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";

mysqli_query($conn, $sql);

header("Location: index.php");

?>