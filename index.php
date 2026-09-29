<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container" style="width: 900px; max-width: 95%;">

    <h2>GESTIÓN DE USUARIOS</h2>

    <form action="insert.php" method="POST">
        <input type="text" name="nombre" placeholder="Nombre completo" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="text" name="rol" placeholder="Rol" value="usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">AGREGAR USUARIO</button>
    </form>

    <br>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>

        <?php
        $resultado = $conn->query("SELECT * FROM usuarios ORDER BY id DESC");

        while ($fila = $resultado->fetch_assoc()) {
            $formulario = 'form' . $fila['id'];
        ?>

        <tr>
            <td>
                <?php echo $fila['id']; ?>
                <form id="<?php echo $formulario; ?>" action="edit.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $fila['id']; ?>">
                </form>
            </td>

            <td>
                <input form="<?php echo $formulario; ?>" type="text" name="nombre" value="<?php echo htmlspecialchars($fila['nombre']); ?>" required>
            </td>

            <td>
                <input form="<?php echo $formulario; ?>" type="email" name="email" value="<?php echo htmlspecialchars($fila['email']); ?>" required>
            </td>

            <td>
                <?php echo htmlspecialchars($fila['rol']); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($fila['estado']); ?>
            </td>

            <td>
                <button form="<?php echo $formulario; ?>" type="submit">Guardar</button>
                <a href="eliminar.php?id=<?php echo $fila['id']; ?>" onclick="return confirm('¿Desea eliminar este usuario?')">Eliminar</a>
            </td>
        </tr>

        <?php
        }
        ?>
    </table>

    <br>
    <a href="dashboard.php"><button>Volver al menú</button></a>

</div>

</body>
</html>