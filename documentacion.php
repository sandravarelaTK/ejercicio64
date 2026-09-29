<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';

$activeTab = $_GET['tab'] ?? 'usuarios';
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editUsuario = ['nombre' => '', 'email' => '', 'rol' => 'usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['actionArchivo'])) {
        $action = $_POST['actionArchivo'];

        if ($action === 'createArchivo') {
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

        if ($action === 'updateArchivo') {
            $id = (int)($_POST['idArchivo'] ?? 0);
            $cliente = trim($_POST['cliente'] ?? '');
            $nombreValidacion = trim($_POST['nombre_validacion'] ?? '');
            $fecha = $_POST['fecha_validacion'] ?? null;
            $responsable = trim($_POST['responsable_validacion'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $archivoDemo = trim($_POST['archivo_demo'] ?? '');

            if ($id > 0 && $cliente !== '' && $nombreValidacion !== '' && $responsable !== '') {
                $stmt = $conn->prepare("UPDATE validaciones_archivos SET cliente = ?, nombre_validacion = ?, fecha_validacion = ?, responsable_validacion = ?, descripcion = ?, archivo_demo = ? WHERE id = ?");
                $stmt->bind_param('ssssssi', $cliente, $nombreValidacion, $fecha, $responsable, $descripcion, $archivoDemo, $id);
                $stmt->execute();
                $stmt->close();
            }
        }

        if ($action === 'deleteArchivo') {
            $id = (int)($_POST['idArchivo'] ?? 0);
            if ($id > 0) {
                $stmt = $conn->prepare("DELETE FROM validaciones_archivos WHERE id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
            }
        }

        header('Location: documentacion.php?tab=archivos');
        exit();
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($nombre !== '' && $email !== '' && $password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol, estado) VALUES (?, ?, ?, ?, 'activo')");
            $rol = $_POST['rol'] ?? 'usuario';
            $stmt->bind_param('ssss', $nombre, $email, $hash, $rol);
            $stmt->execute();
            $stmt->close();
        }
    }

    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $rol = $_POST['rol'] ?? 'usuario';
        $password = $_POST['password'] ?? '';

        if ($id > 0 && $nombre !== '' && $email !== '') {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ?, password = ? WHERE id = ?");
                $stmt->bind_param('ssssi', $nombre, $email, $rol, $hash, $id);
            } else {
                $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?");
                $stmt->bind_param('sssi', $nombre, $email, $rol, $id);
            }
            $stmt->execute();
            $stmt->close();
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    header('Location: documentacion.php?tab=usuarios');
    exit();
}

if ($editId > 0) {
    $stmt = $conn->prepare("SELECT nombre, email, rol FROM usuarios WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $editUsuario = $result->fetch_assoc();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión documental</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .tab-nav { display: flex; gap: 12px; margin-bottom: 20px; }
        .tab-nav a { text-decoration: none; flex: 1; }
        .tab-nav button { width: 100%; }
        .tab-nav .active { background-color: #d32f2f; }
        .module-box { display: none; }
        .module-box.active { display: block; }
        .small-btn { width: auto; padding: 8px 12px; margin: 0 5px; }
        .form-inline { display: flex; gap: 10px; flex-wrap: wrap; }
        .form-inline input, .form-inline select { flex: 1; min-width: 180px; }
    </style>
</head>
<body>

<div class="container" style="width: 980px; max-width: 95%;">
    <h2>GESTIÓN DEL SISTEMA</h2>

    <div class="tab-nav">
        <a href="documentacion.php?tab=usuarios"><button class="<?php echo $activeTab === 'usuarios' ? 'active' : ''; ?>">Gestión de Usuarios</button></a>
        <a href="documentacion.php?tab=archivos"><button class="<?php echo $activeTab === 'archivos' ? 'active' : ''; ?>">Gestión de Archivos</button></a>
    </div>

    <div class="module-box <?php echo $activeTab === 'usuarios' ? 'active' : ''; ?>">
        <h3>Usuarios</h3>

        <form action="documentacion.php?tab=usuarios" method="POST">
            <input type="hidden" name="action" value="<?php echo $editId > 0 ? 'update' : 'create'; ?>">
            <?php if ($editId > 0) { ?>
                <input type="hidden" name="id" value="<?php echo $editId; ?>">
            <?php } ?>

            <div class="form-inline">
                <input type="text" name="nombre" placeholder="Nombre completo" value="<?php echo htmlspecialchars($editUsuario['nombre']); ?>" required>
                <input type="email" name="email" placeholder="Correo electrónico" value="<?php echo htmlspecialchars($editUsuario['email']); ?>" required>
                <input type="password" name="password" placeholder="<?php echo $editId > 0 ? 'Nueva contraseña (opcional)' : 'Contraseña'; ?>" <?php echo $editId > 0 ? '' : 'required'; ?>>
                <input type="text" name="rol" placeholder="Rol" value="<?php echo htmlspecialchars($editUsuario['rol'] ?? 'usuario'); ?>" required>
            </div>

            <button type="submit"><?php echo $editId > 0 ? 'Actualizar usuario' : 'Agregar usuario'; ?></button>
            <?php if ($editId > 0) { ?>
                <a href="documentacion.php?tab=usuarios"><button type="button" class="small-btn">Cancelar</button></a>
            <?php } ?>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            <?php
            $usuarios = $conn->query("SELECT * FROM usuarios ORDER BY id DESC");
            while ($usuario = $usuarios->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $usuario['id'] . '</td>';
                echo '<td>' . htmlspecialchars($usuario['nombre']) . '</td>';
                echo '<td>' . htmlspecialchars($usuario['email']) . '</td>';
                echo '<td>' . htmlspecialchars($usuario['rol']) . '</td>';
                echo '<td>' . htmlspecialchars($usuario['estado']) . '</td>';
                echo '<td>';
                echo '<a href="documentacion.php?tab=usuarios&edit=' . $usuario['id'] . '"><button type="button" class="small-btn">Editar</button></a>';
                echo '<form action="documentacion.php?tab=usuarios" method="POST" style="display:inline;">';
                echo '<input type="hidden" name="action" value="delete">';
                echo '<input type="hidden" name="id" value="' . $usuario['id'] . '">';
                echo '<button type="submit" class="small-btn" onclick="return confirm(\'¿Desea eliminar este usuario?\')">Eliminar</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </table>
    </div>

    <div class="module-box <?php echo $activeTab === 'archivos' ? 'active' : ''; ?>">
        <h3>Gestión de Archivos</h3>

        <?php
        $editArchivoId = isset($_GET['editArchivo']) ? (int)$_GET['editArchivo'] : 0;
        $editArchivo = [
            'cliente' => '',
            'nombre_validacion' => '',
            'fecha_validacion' => '',
            'responsable_validacion' => '',
            'descripcion' => '',
            'archivo_demo' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['actionArchivo'] ?? '') !== '') {
            $action = $_POST['actionArchivo'];

            if ($action === 'createArchivo') {
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

            if ($action === 'updateArchivo') {
                $id = (int)($_POST['idArchivo'] ?? 0);
                $cliente = trim($_POST['cliente'] ?? '');
                $nombreValidacion = trim($_POST['nombre_validacion'] ?? '');
                $fecha = $_POST['fecha_validacion'] ?? null;
                $responsable = trim($_POST['responsable_validacion'] ?? '');
                $descripcion = trim($_POST['descripcion'] ?? '');
                $archivoDemo = trim($_POST['archivo_demo'] ?? '');

                if ($id > 0 && $cliente !== '' && $nombreValidacion !== '' && $responsable !== '') {
                    $stmt = $conn->prepare("UPDATE validaciones_archivos SET cliente = ?, nombre_validacion = ?, fecha_validacion = ?, responsable_validacion = ?, descripcion = ?, archivo_demo = ? WHERE id = ?");
                    $stmt->bind_param('ssssssi', $cliente, $nombreValidacion, $fecha, $responsable, $descripcion, $archivoDemo, $id);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            if ($action === 'deleteArchivo') {
                $id = (int)($_POST['idArchivo'] ?? 0);
                if ($id > 0) {
                    $stmt = $conn->prepare("DELETE FROM validaciones_archivos WHERE id = ?");
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            header('Location: documentacion.php?tab=archivos');
            exit();
        }

        if ($editArchivoId > 0) {
            $stmt = $conn->prepare("SELECT cliente, nombre_validacion, fecha_validacion, responsable_validacion, descripcion, archivo_demo FROM validaciones_archivos WHERE id = ? LIMIT 1");
            $stmt->bind_param('i', $editArchivoId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $editArchivo = $result->fetch_assoc();
            }
            $stmt->close();
        }
        ?>

        <form action="documentacion.php?tab=archivos" method="POST">
            <input type="hidden" name="actionArchivo" value="<?php echo $editArchivoId > 0 ? 'updateArchivo' : 'createArchivo'; ?>">
            <?php if ($editArchivoId > 0) { ?>
                <input type="hidden" name="idArchivo" value="<?php echo $editArchivoId; ?>">
            <?php } ?>

            <div class="form-inline">
                <input type="text" name="cliente" placeholder="Cliente" value="<?php echo htmlspecialchars($editArchivo['cliente']); ?>" required>
                <input type="text" name="nombre_validacion" placeholder="Nombre de la validación" value="<?php echo htmlspecialchars($editArchivo['nombre_validacion']); ?>" required>
                <input type="date" name="fecha_validacion" value="<?php echo htmlspecialchars($editArchivo['fecha_validacion']); ?>" required>
                <input type="text" name="responsable_validacion" placeholder="Responsable de la validación" value="<?php echo htmlspecialchars($editArchivo['responsable_validacion']); ?>" required>
            </div>

            <textarea name="descripcion" rows="3" placeholder="Descripción o observaciones"><?php echo htmlspecialchars($editArchivo['descripcion']); ?></textarea>

            <div class="form-inline">
                <input type="text" name="archivo_demo" id="archivo_demo" placeholder="Nombre del archivo adjunto (demo)" value="<?php echo htmlspecialchars($editArchivo['archivo_demo']); ?>">
                <button type="button" class="small-btn" onclick="document.getElementById('archivo_demo').value = 'validacion-demo.pdf'; alert('Archivo de validación cargado de ejemplo');">Subir archivo demo</button>
            </div>

            <button type="submit"><?php echo $editArchivoId > 0 ? 'Actualizar validación' : 'Guardar validación'; ?></button>
            <?php if ($editArchivoId > 0) { ?>
                <a href="documentacion.php?tab=archivos"><button type="button" class="small-btn">Cancelar</button></a>
            <?php } ?>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Validación</th>
                <th>Fecha</th>
                <th>Responsable</th>
                <th>Archivo</th>
                <th>Acciones</th>
            </tr>
            <?php
            $archivos = $conn->query("SELECT * FROM validaciones_archivos ORDER BY id DESC");
            while ($archivo = $archivos->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $archivo['id'] . '</td>';
                echo '<td>' . htmlspecialchars($archivo['cliente']) . '</td>';
                echo '<td>' . htmlspecialchars($archivo['nombre_validacion']) . '</td>';
                echo '<td>' . htmlspecialchars($archivo['fecha_validacion']) . '</td>';
                echo '<td>' . htmlspecialchars($archivo['responsable_validacion']) . '</td>';
                echo '<td>' . htmlspecialchars($archivo['archivo_demo'] ?: 'Sin archivo') . '</td>';
                echo '<td>';
                echo '<a href="documentacion.php?tab=archivos&editArchivo=' . $archivo['id'] . '"><button type="button" class="small-btn">Editar</button></a>';
                echo '<form action="documentacion.php?tab=archivos" method="POST" style="display:inline;">';
                echo '<input type="hidden" name="actionArchivo" value="deleteArchivo">';
                echo '<input type="hidden" name="idArchivo" value="' . $archivo['id'] . '">';
                echo '<button type="submit" class="small-btn" onclick="return confirm(\'¿Desea eliminar esta validación?\')">Eliminar</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </table>
    </div>

    <br>
    <a href="dashboard.php"><button>Volver al menú</button></a>
</div>

</body>
</html>