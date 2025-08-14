<?php
// core/functions.php

// Este archivo contiene funciones centrales para la conexión a la base de datos
// y la gestión de usuarios, diseñado para ser incluido en otras partes de la aplicación.


/**
 * Función para conectar a la base de datos MySQL.
 * Utiliza la variable de entorno JAWSDB_URL (o DATABASE_URL) proporcionada por Heroku.
 * Opcionalmente, usa credenciales locales si las variables de entorno no están configuradas.
 *
 * @return mysqli Un objeto de conexión a la base de datos.
 */
function connectDB() {
    // Intentar obtener la URL de JawsDB
    $url = getenv("JAWSDB_URL");

    // Si JAWSDB_URL no está configurada, probar con DATABASE_URL (otra común en Heroku)
    if (empty($url)) {
        $url = getenv("DATABASE_URL");
    }

    // Si ninguna variable de entorno de Heroku está presente, usar credenciales locales
    if (empty($url)) {
        $host = '127.0.0.1'; // Host para XAMPP/local
        $user = 'root';     // Tu usuario local de MySQL
        $password = 'Dan19060..'; // ¡TU CONTRASEÑA LOCAL!
        $db = 'Noxiew';     // El nombre de tu base de datos local
    } else {
        // Parsear la URL de Heroku para extraer las credenciales
        $dbparts = parse_url($url);
        $host = $dbparts['host'];
        $user = $dbparts['user'];
        $password = $dbparts['pass'];
        // Eliminar la barra inicial del path de la DB
        $db = ltrim($dbparts['path'],'/');
    }

    // Conexión usando MySQLi (orientado a objetos)
    $conn = new mysqli($host, $user, $password, $db);

    // Verificar la conexión
    if ($conn->connect_error) {
        // En un entorno de producción, es mejor registrar este error y mostrar
        // una página de error genérica en lugar de `die()`.
        die("Error de conexión a la base de datos: " . $conn->connect_error);
    }
    return $conn;
}

// --- Funciones de Gestión de Usuarios (Ahora con MySQL) ---

/**
 * Crea la tabla de usuarios si no existe.
 * Esta función es útil para el primer despliegue o la inicialización de la base de datos.
 */
function createUsersTable() {
    $conn = connectDB();
    // La columna 'note' se añade para almacenar notas personales de los usuarios.
    // PASSWORD_HASH: En producción, se usaría esto junto con password_verify() para seguridad.
    $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                note TEXT
            )";
    if ($conn->query($sql) === TRUE) {
        // Puedes añadir un log o mensaje de éxito si lo deseas, pero no es necesario para el funcionamiento.
    } else {
        error_log("Error al crear la tabla 'users': " . $conn->error); // Registrar el error
        // En producción, no mostrar el error directamente al usuario.
    }
    $conn->close();
}

// Ejecutar la creación de la tabla al incluir functions.php.
// Esto asegura que la tabla exista al inicio de la aplicación.
// Si prefieres un sistema de migración más robusto, esta llamada puede ser removida.
createUsersTable();


/**
 * Busca un usuario por su nombre de usuario en la base de datos.
 *
 * @param string $username El nombre de usuario a buscar.
 * @return array|null Un array asociativo con los datos del usuario si se encuentra, o null si no.
 */
function findUserByUsername($username) {
    $conn = connectDB();
    // Preparar la consulta para prevenir inyecciones SQL
    $stmt = $conn->prepare("SELECT id, username, password, note FROM users WHERE username = ?");
    $stmt->bind_param("s", $username); // "s" indica que el parámetro es una cadena
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc(); // Obtener el usuario como un array asociativo
    $stmt->close();
    $conn->close();
    return $user;
}

/**
 * Registra un nuevo usuario en la base de datos.
 *
 * @param string $username El nombre de usuario.
 * @param string $password La contraseña (ADVERTENCIA: ¡NO HASHADA EN ESTA DEMO! USAR password_hash() EN PRODUCCIÓN).
 * @return bool True si el registro es exitoso, false si el usuario ya existe o hay un error.
 */
function registerNewUser($username, $password) {
    $conn = connectDB();
    // Primero, verifica si el usuario ya existe para evitar errores de unicidad
    if (findUserByUsername($username)) {
        $conn->close();
        return false; // Usuario ya existe
    }

    // ADVERTENCIA DE SEGURIDAD: En un entorno de producción, DEBES hashear la contraseña:
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // Y luego usar $hashed_password en la consulta.
    $stmt = $conn->prepare("INSERT INTO users (username, password, note) VALUES (?, ?, ?)");
    $emptyNote = ''; // Nota inicial vacía para nuevos usuarios
    $stmt->bind_param("sss", $username, $password, $emptyNote); // "sss" para 3 cadenas

    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

/**
 * Actualiza la nota personal de un usuario en la base de datos.
 *
 * @param string $username El nombre de usuario.
 * @param string $newNote La nueva nota a guardar.
 * @return bool True si la nota se actualizó, false si el usuario no se encontró o hay un error.
 */
function updateUserNote($username, $newNote) {
    $conn = connectDB();
    $stmt = $conn->prepare("UPDATE users SET note = ? WHERE username = ?");
    $stmt->bind_param("ss", $newNote, $username); // "ss" para 2 cadenas
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

/**
 * Redirige al usuario a otra página utilizando el encabezado Location.
 * IMPORTANTE: Esta función debe ser llamada antes de que se envíe cualquier HTML al navegador.
 *
 * @param string $location La URL a la que redirigir.
 */
function redirectTo($location) {
    header('Location: ' . $location);
    exit(); // Es crucial llamar a exit() después de un header Location para evitar que se ejecute más código.
}

/**
 * Verifica si el usuario está autenticado comprobando la sesión y buscando el usuario en la DB.
 *
 * @return array|null Un array asociativo con los datos del usuario si está logueado, o null si no.
 */
function isAuthenticated() {
    // Asegúrate de que la sesión se haya iniciado antes de usar $_SESSION
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['username'])) {
        return findUserByUsername($_SESSION['username']);
    }
    return null;
}
?>
