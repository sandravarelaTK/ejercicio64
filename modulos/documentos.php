<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit();
}

include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = trim($_POST['cliente'] ?? '');
    $nombreValidacion = trim($_POST['nombre_validacion'] ?? '');
    $fecha = $_POST['fecha_validacion'] ?? null;
    $responsable = trim($_POST['responsable_validacion'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $archivoDemo = trim($_POST['archivo_demo'] ?? '');

    if ($cliente !== '' && $nombreValidacion !== '' && $responsable !== '') {
        $stmt = $conn->prepare("INSERT INTO validaciones_archivos (cliente, nombre_validacion, fecha_validacion, responsable_validacion, descripcion, archivo_demo, estado) VALUES (?, ?, ?, ?, ?, ?, 'activo')");
        $stmt->bind_param('ssssss', $cliente, $nombreValidacion, $fecha, $responsable, $descripcion, $archivoDemo);
        $stmt->execute();
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Archivos</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container" style="width: 980px; max-width: 95%;">

    <h2>GESTIÓN DE ARCHIVOS</h2>

    <form method="POST">
        <input type="text" name="cliente" placeholder="Cliente" required>
        <input type="text" name="nombre_validacion" placeholder="Nombre de la validación" required>
        <input type="date" name="fecha_validacion" required>
        <input type="text" name="responsable_validacion" placeholder="Responsable de la validación" required>
        <textarea name="descripcion" rows="3" placeholder="Descripción o observaciones"></textarea>
        <input type="text" name="archivo_demo" id="archivo_demo" placeholder="Archivo adjunto (demo)">
        <button type="button" onclick="document.getElementById('archivo_demo').value = 'validacion-demo.pdf'; alert('Archivo de validación cargado de ejemplo');">Subir archivo demo</button>
        <button type="submit">Guardar validación</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Validación</th>
            <th>Fecha</th>
            <th>Responsable</th>
            <th>Archivo</th>
        </tr>
        <?php
        $resultado = $conn->query("SELECT * FROM validaciones_archivos ORDER BY id DESC");
        while ($fila = $resultado->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $fila['id'] . '</td>';
            echo '<td>' . htmlspecialchars($fila['cliente']) . '</td>';
            echo '<td>' . htmlspecialchars($fila['nombre_validacion']) . '</td>';
            echo '<td>' . htmlspecialchars($fila['fecha_validacion']) . '</td>';
            echo '<td>' . htmlspecialchars($fila['responsable_validacion']) . '</td>';
            echo '<td>' . htmlspecialchars($fila['archivo_demo'] ?: 'Sin archivo') . '</td>';
            echo '</tr>';
        }
        ?>
    </table>

    <br>
    <a href="../dashboard.php"><button>Volver al menú</button></a>

</div>

</body>
</html>