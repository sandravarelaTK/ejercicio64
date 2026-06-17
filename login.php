<?php
session_start();
include 'db.php';
$error = '';

if (isset($_SESSION['usuario'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = mysqli_real_escape_string($conn, $_POST['usuario']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT id FROM usuarios WHERE nombre='$usuario' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['usuario'] = $usuario;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
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
    <?php if ($error != '') { echo "<p class='error-message'>$error</p>"; } ?>

    <form method="POST">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">INICIAR SESIÓN</button>
    </form>

    <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>.</p>
</div>
</body>
</html>