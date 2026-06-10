<?php

$conn = new mysqli("localhost", "root", "", "crud_app6");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    
}
