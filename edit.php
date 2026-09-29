<?php

session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($id > 0 && $nombre !== '' && $email !== '') {
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?");
        $stmt->bind_param('ssi', $nombre, $email, $id);
        $stmt->execute();
        $stmt->close();
    }
}

header('Location: index.php');
exit();