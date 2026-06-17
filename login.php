<?php

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = $_POST["usuario"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nombre = ? AND password = ?");
    $stmt->bind_param("ss", $usuario, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }

    $stmt->close();
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

    <div class="login-logo">
        <img src="vps-logo.svg" alt="Logo VPS">
        <p>Sistema de Gestión Documental</p>
    </div>

    <h2>INICIO DE SESIÓN</h2>
    <?php
    if(isset($error)){
        echo "<p class=\"error-message\">$error</p>";
    }
    ?>

    <form method="POST">

        <input
            type="text"
            name="usuario"
            placeholder="Usuario"
            required>

        <input
            type="password"
            name="password"
            placeholder="Contraseña"
            required>

        <button type="submit">
            INICIAR SESIÓN
        </button>

    </form>

    <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>.</p>

</div>

</body>
</html>