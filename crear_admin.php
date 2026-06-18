<?php
require_once 'db.php';

$nombre = 'admin';
$email = 'admin@gmail.com';
$password = password_hash('123456', PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $nombre, $email, $password);

if ($stmt->execute()) {
    echo 'Administrador creado';
} else {
    echo 'ERROR: ' . $conn->error;
}
?>