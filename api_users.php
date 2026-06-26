<?php
header('Content-Type: application/json; charset=utf-8');

include 'db.php';

$sql = "SELECT id, name, email FROM users ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'No se pudo consultar la tabla users'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $users
], JSON_UNESCAPED_UNICODE);
?>
