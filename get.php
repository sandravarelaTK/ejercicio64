<?php

include 'db.php';

header('Content-Type: application/json; charset=utf-8');

$result = $conn->query("SELECT id, nombre AS name, email FROM usuarios ORDER BY id DESC");

if (!$result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo consultar la tabla usuarios'], JSON_UNESCAPED_UNICODE);
    exit();
}

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode(['success' => true, 'data' => $users], JSON_UNESCAPED_UNICODE);
