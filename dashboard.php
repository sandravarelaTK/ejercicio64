<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard VPS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h1>SISTEMA DE GESTIÓN DOCUMENTAL VPS</h1>
    <h3>Menú Principal</h3>

    <div class="menu-dashboard">

        <a href="index.php">
            <button>Gestión de Usuarios</button>
        </a>

        <a href="modulos/documentos.php">
            <button>Gestión Documental</button>
        </a>

        <a href="modulos/versiones.php">
            <button>Control de Versiones</button>
        </a>

        <a href="modulos/aprobaciones.php">
            <button>Aprobaciones</button>
        </a>

        <a href="modulos/auditoria.php">
            <button>Auditoría</button>
        </a>

        <a href="modulos/reportes.php">
            <button>Reportes</button>
        </a>

        <a href="modulos/configuracion.php">
            <button>Configuración</button>
        </a>

    </div>

</div>

</body>
</html>
