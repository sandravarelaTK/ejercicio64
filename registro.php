<?php
session_start();
include 'db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if ($nombre === '' || $email === '' || $password === '') {
            $error = 'Por favor completa todos los campos para registrarte.';
        } else {
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nombre = ? OR email = ?");
            $stmt->bind_param("ss", $nombre, $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = 'El usuario o el correo ya existen. Usa otro nombre o correo.';
            } else {
                $stmt->close();
                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $nombre, $email, $password);
                $stmt->execute();
                $stmt->close();

                $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
                $stmt->bind_param("ss", $nombre, $email);
                $stmt->execute();
                $stmt->close();

                $success = 'Registro exitoso. Ahora puedes iniciar sesión desde el enlace al login.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Registro de Usuario</h2>

    <?php if ($error !== ''): ?>
        <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success !== ''): ?>
        <p class="success-message"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="action" value="register">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Registrar</button>
    </form>

    <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>.</p>
</div>

</body>
</html>
