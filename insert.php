<?php

session_start();
include 'db.php';

function wantsJson()
{
    return isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (wantsJson()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit();
    }

    header('Location: index.php');
    exit();
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($name === '' || $email === '') {
    if (wantsJson()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Nombre y correo son obligatorios']);
        exit();
    }

    header('Location: index.php');
    exit();
}

$name_safe = mysqli_real_escape_string($conn, $name);
$email_safe = mysqli_real_escape_string($conn, $email);

$sql = "INSERT INTO users (name, email) VALUES ('$name_safe', '$email_safe')";

if (mysqli_query($conn, $sql)) {
    if (wantsJson()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Usuario agregado correctamente',
            'id' => mysqli_insert_id($conn)
        ]);
        exit();
    }

    header('Location: index.php');
    exit();
}

if (wantsJson()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No se pudo agregar el usuario']);
    exit();
}

header('Location: index.php');
exit();

?>