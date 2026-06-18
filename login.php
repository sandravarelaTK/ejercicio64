<?php
session_start();
include 'db.php';
$error = '';

if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    $usuario_safe = mysqli_real_escape_string($conn, $usuario);
    $sql = "SELECT password FROM usuarios WHERE LOWER(nombre)=LOWER('$usuario_safe') OR LOWER(email)=LOWER('$usuario_safe')";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['usuario'] = $usuario;
            header('Location: index.php');
            exit();
        }
    }

    $error = 'Usuario o contraseña incorrectos';
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

</div>
</body>
</html>