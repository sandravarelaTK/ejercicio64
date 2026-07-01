<?php

include 'db.php';

$name = $_REQUEST['name'];
$email = $_REQUEST['email'];

$name_safe = mysqli_real_escape_string($conn, $name);
$email_safe = mysqli_real_escape_string($conn, $email);

$sql = "INSERT INTO users (name, email) VALUES ('$name_safe', '$email_safe')";

$conn->query($sql);

header("Location: index.php");

?>
