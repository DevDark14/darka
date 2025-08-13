<?php
// functions.php

// Función para conectar a la base de datos MySQL.
// Utiliza la variable de entorno CLEARDB_DATABASE_URL proporcionada por Heroku.
function connectDB() {
    $url = getenv("CLEARDB_DATABASE_URL"); // Obtiene la URL de la base de datos de Heroku
    if (empty($url)) {
        // En desarrollo local, puedes definir tus propias credenciales aquí
        // O configurar una base de datos local (ej. XAMPP MySQL)
        $host = '127.0.0.1'; // Host para XAMPP/local
        $user = 'root'; // Tu usuario local de MySQL
        $password = 'Dan19060..'; // ¡TU CONTRASEÑA LOCAL!
        $db = 'Noxiew'; // El nombre de tu base de datos local (según la imagen/tus datos)
    } else {
        // Parsear la URL de Heroku
        $dbparts = parse_url($url);
        $host = $dbparts['host'];
        $user = $dbparts['user'];
        $password = $dbparts['pass'];
        $db = ltrim($dbparts['path'],'/');
    }

    // Conexión usando MySQLi (orientado a objetos)
    $conn = new mysqli($host, $user, $password, $db);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Error de conexión a la base de datos: " . $conn->connect_error);
    }
    return $conn;
}

// --- Funciones de Gestión de Usuarios (Ahora con MySQL) ---

/**
 * Crea la tabla de usuarios si no existe.
 * Esto es bueno para ejecutar una vez en el primer despliegue o al inicializar.
 */
function createUsersTable() {
    $conn = connectDB();
    // La columna 'note' se añade con un valor predeterminado si el usuario no tiene una.
    // PASSWORD_HASH: En producción, usar esto con password_verify.
    // CORRECCIÓN: Se eliminó DEFAULT '' para el tipo TEXT, ya que MySQL no lo permite directamente.
    $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                note TEXT
            )";
    if ($conn->query($sql) === TRUE) {
        //echo "Tabla 'users' creada exitosamente o ya existe.<br>";
    } else {
        echo "Error al crear la tabla: " . $conn->error . "<br>";
    }
    $conn->close();
}

// Ejecutar la creación de la tabla al incluir functions.php (solo una vez)
// Puedes comentar esta línea después del primer despliegue si la tabla ya existe
// o crear un script de migración para entornos más complejos.
createUsersTable();


/**
 * Busca un usuario por nombre de usuario en la base de datos.
 * @param string $username El nombre de usuario a buscar.
 * @return array|null El array de usuario si se encuentra, o null.
 */
function findUserByUsername($username) {
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT id, username, password, note FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $user;
}

/**
 * Registra un nuevo usuario en la base de datos.
 * @param string $username El nombre de usuario.
 * @param string $password La contraseña (sin hash para esta demo, ¡no usar en producción!).
 * @return bool True si el registro es exitoso, false si el usuario ya existe o hay un error.
 */
function registerNewUser($username, $password) {
    $conn = connectDB();
    // Primero, verifica si el usuario ya existe para evitar errores UNIQUE
    if (findUserByUsername($username)) {
        $conn->close();
        return false; // Usuario ya existe
    }

    // ADVERTENCIA DE SEGURIDAD: En producción, aquí harías:
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // Y guardarías $hashed_password en la DB.
    $stmt = $conn->prepare("INSERT INTO users (username, password, note) VALUES (?, ?, ?)");
    $emptyNote = ''; // Nota inicial vacía
    $stmt->bind_param("sss", $username, $password, $emptyNote); // "sss" para 3 strings

    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

/**
 * Actualiza la nota personal de un usuario en la base de datos.
 * @param string $username El nombre de usuario.
 * @param string $newNote La nueva nota.
 * @return bool True si la nota se actualizó, false si el usuario no se encontró o hay un error.
 */
function updateUserNote($username, $newNote) {
    $conn = connectDB();
    $stmt = $conn->prepare("UPDATE users SET note = ? WHERE username = ?");
    $stmt->bind_param("ss", $newNote, $username); // "ss" para 2 strings
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

/**
 * Redirige al usuario a otra página.
 * @param string $location La URL a la que redirigir.
 */
function redirectTo($location) {
    header('Location: ' . $location);
    exit();
}

/**
 * Verifica si el usuario está autenticado y devuelve sus datos.
 * @return array|null El objeto de usuario si está logueado, o null.
 */
function isAuthenticated() {
    if (isset($_SESSION['username'])) {
        return findUserByUsername($_SESSION['username']);
    }
    return null;
}
?>
