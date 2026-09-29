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
    <style>
        body {
            display: block;
            padding: 30px 20px;
            background: #f5f5f5;
        }
        .dashboard-wrap {
            max-width: 620px;
            margin: 80px auto 0;
            background: #ffffff;
            border-radius: 12px;
            padding: 30px 20px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        }
        .dashboard-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
        }
        .dashboard-actions a {
            text-decoration: none;
            width: 220px;
        }
        .dashboard-actions button {
            width: 100%;
            height: 52px;
            font-size: 15px;
            margin: 0;
        }
    </style>
</head>
<body>

<div class="dashboard-wrap">
    <div class="dashboard-actions">
        <a href="documentacion.php?tab=usuarios"><button>Usuarios</button></a>
        <a href="documentacion.php?tab=archivos"><button>Archivos</button></a>
    </div>
</div>

</body>
</html>
