<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Formulario Sandra Varela</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h2>FORMULARIO CLIENTES VPS</h2>

    <form action="insert.php" method="POST">

        <input
            type="text"
            name="name"
            placeholder="Ingresar Nombre"
            required>

        <input
            type="email"
            name="email"
            placeholder="Ingresar Correo"
            required>

        <button type="submit">
            AGREGAR USUARIO
        </button>

    </form>

    <br>

    <table>

        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Acción</th>
        </tr>

        <?php
        $resultado = mysqli_query($conn, "SELECT * FROM users");

        while($fila = mysqli_fetch_assoc($resultado)) {
            $formulario = "form" . $fila['id'];
        ?>

        <tr>

            <td>
                <?php echo $fila['id']; ?>

                <form id="<?php echo $formulario; ?>" action="edit.php" method="POST">
                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo $fila['id']; ?>">
                </form>
            </td>

            <td>
                <input
                    form="<?php echo $formulario; ?>"
                    type="text"
                    name="name"
                    value="<?php echo $fila['name']; ?>"
                    required>
            </td>

            <td>
                <input
                    form="<?php echo $formulario; ?>"
                    type="email"
                    name="email"
                    value="<?php echo $fila['email']; ?>"
                    required>
            </td>

            <td>
                <button
                    form="<?php echo $formulario; ?>"
                    type="submit">
                    Guardar
                </button>

                <a href="eliminar.php?id=<?php echo $fila['id']; ?>"
                   onclick="return confirm('¿Desea eliminar este usuario?')">
                    Eliminar
                </a>
            </td>

        </tr>

        <?php
        }
        ?>

    </table>

</div>

</body>
</html>