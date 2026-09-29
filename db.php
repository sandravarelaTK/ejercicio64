<?php

$conn = new mysqli("localhost", "root", "", "crud_app6");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

function ensureDatabaseSchema($conn)
{
    $createQueries = [
        "CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            password VARCHAR(255) NOT NULL,
            rol VARCHAR(50) NOT NULL DEFAULT 'usuario',
            estado VARCHAR(20) NOT NULL DEFAULT 'activo',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS validaciones_archivos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cliente VARCHAR(150) NOT NULL DEFAULT '',
            nombre_validacion VARCHAR(200) NOT NULL DEFAULT '',
            fecha_validacion DATE NULL,
            responsable_validacion VARCHAR(150) NOT NULL DEFAULT '',
            descripcion TEXT,
            archivo_demo VARCHAR(255) NOT NULL DEFAULT '',
            estado VARCHAR(20) NOT NULL DEFAULT 'activo',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS archivos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cliente VARCHAR(150) NOT NULL DEFAULT '',
            nombre_validacion VARCHAR(200) NOT NULL DEFAULT '',
            fecha_validacion DATE NULL,
            responsable_validacion VARCHAR(150) NOT NULL DEFAULT '',
            descripcion TEXT,
            archivo_demo VARCHAR(255) NOT NULL DEFAULT '',
            estado VARCHAR(20) NOT NULL DEFAULT 'activo',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ];

    foreach ($createQueries as $query) {
        if (!$conn->query($query)) {
            throw new Exception("Error al crear la estructura: " . $conn->error);
        }
    }

    $columnsUsuarios = [];
    $colResult = $conn->query("SHOW COLUMNS FROM usuarios");
    while ($col = $colResult->fetch_assoc()) {
        $columnsUsuarios[] = $col['Field'];
    }

    foreach (['rol', 'estado', 'created_at'] as $column) {
        if (!in_array($column, $columnsUsuarios, true)) {
            $alter = match ($column) {
                'rol' => "ALTER TABLE usuarios ADD COLUMN rol VARCHAR(50) NOT NULL DEFAULT 'usuario'",
                'estado' => "ALTER TABLE usuarios ADD COLUMN estado VARCHAR(20) NOT NULL DEFAULT 'activo'",
                'created_at' => "ALTER TABLE usuarios ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            };
            if (!$conn->query($alter)) {
                throw new Exception("Error al ajustar columnas de usuarios: " . $conn->error);
            }
        }
    }

    $columnsArchivos = [];
    $colArchivos = $conn->query("SHOW COLUMNS FROM archivos");
    while ($col = $colArchivos->fetch_assoc()) {
        $columnsArchivos[] = $col['Field'];
    }

    $archivoColumns = [
        'cliente' => "ALTER TABLE archivos ADD COLUMN cliente VARCHAR(150) NOT NULL DEFAULT ''",
        'nombre_validacion' => "ALTER TABLE archivos ADD COLUMN nombre_validacion VARCHAR(200) NOT NULL DEFAULT ''",
        'fecha_validacion' => "ALTER TABLE archivos ADD COLUMN fecha_validacion DATE NULL",
        'responsable_validacion' => "ALTER TABLE archivos ADD COLUMN responsable_validacion VARCHAR(150) NOT NULL DEFAULT ''",
        'descripcion' => "ALTER TABLE archivos ADD COLUMN descripcion TEXT",
        'archivo_demo' => "ALTER TABLE archivos ADD COLUMN archivo_demo VARCHAR(255) NOT NULL DEFAULT ''",
        'ruta' => "ALTER TABLE archivos ADD COLUMN ruta VARCHAR(255) DEFAULT ''",
        'usuario_id' => "ALTER TABLE archivos ADD COLUMN usuario_id INT NULL",
        'estado' => "ALTER TABLE archivos ADD COLUMN estado VARCHAR(20) NOT NULL DEFAULT 'activo'",
        'created_at' => "ALTER TABLE archivos ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
    ];

    foreach ($archivoColumns as $column => $alter) {
        if (!in_array($column, $columnsArchivos, true)) {
            $result = $conn->query($alter);
            if ($result === false) {
                $error = $conn->error;
                if (stripos($error, 'duplicate column') === false && stripos($error, 'already exists') === false) {
                    throw new Exception("Error al ajustar columnas de archivos: " . $error);
                }
            }
        }
    }

    $checkUnique = $conn->query("SHOW INDEX FROM usuarios WHERE Column_name = 'email' AND Non_unique = 0");
    if ($checkUnique && $checkUnique->num_rows === 0) {
        $conn->query("CREATE UNIQUE INDEX IF NOT EXISTS email_unique ON usuarios (email)");
    }

    $checkAdmin = $conn->query("SELECT id FROM usuarios WHERE email = 'admin@gmail.com' LIMIT 1");
    if ($checkAdmin && $checkAdmin->num_rows === 0) {
        $adminPassword = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $email, $adminPassword, $rol);
        $nombre = 'admin';
        $email = 'admin@gmail.com';
        $rol = 'admin';
        $stmt->execute();
        $stmt->close();
    }
}


try {
    ensureDatabaseSchema($conn);
} catch (Exception $e) {
    die($e->getMessage());
}
