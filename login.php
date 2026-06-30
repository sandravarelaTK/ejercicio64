<?php
session_start();
include 'db.php';

function wantsJson()
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    return stripos($accept, 'application/json') !== false || stripos($accept, 'text/html') === false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') !== false) {
        $json = json_decode(file_get_contents('php://input'), true);
        if (is_array($json)) {
            $_POST = array_merge($_POST, $json);
        }
    }

    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usuario !== '' && $password !== '') {
        $usuario_safe = mysqli_real_escape_string($conn, $usuario);
        $sql = "SELECT password FROM usuarios WHERE LOWER(nombre)=LOWER('$usuario_safe') OR LOWER(email)=LOWER('$usuario_safe') LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                $_SESSION['usuario'] = $usuario;
                if (wantsJson()) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['success' => true, 'message' => 'Login exitoso']);
                    exit();
                }
                header('Location: index.php');
                exit();
            }
        }
    }

    if (wantsJson()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
        exit();
    }
}

if (!wantsJson()) {
    header('Content-Type: text/html; charset=utf-8');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login VPS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>INICIO DE SESIÓN</h2>

    <form method="POST">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">INICIAR SESIÓN</button>
    </form>

</div>
</body>
</html>