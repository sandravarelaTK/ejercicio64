<?php

include 'db.php';

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$rol = trim($_POST['rol'] ?? 'usuario');

if ($nombre === '' || $email === '' || $password === '' || $rol === '') {
    header('Location: index.php');
    exit();
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol, estado) VALUES (?, ?, ?, ?, 'activo')");
$stmt->bind_param('ssss', $nombre, $email, $hash, $rol);

if ($stmt->execute()) {
    $_SESSION['mensaje'] = 'Usuario agregado correctamente';
} else {
    $_SESSION['mensaje'] = 'No se pudo crear el usuario: ' . $conn->error;
}

$stmt->close();
$conn->close();
header('Location: index.php');
exit();
