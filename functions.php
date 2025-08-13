<?php
// functions.php

// Define el archivo donde se almacenarán los datos de los usuarios (simulando una DB)
define('USERS_FILE', 'users.json');

/**
 * Carga los usuarios desde el archivo JSON.
 * @return array Un array de objetos (arrays asociativos en PHP) de usuario.
 */
function getUsers() {
    // Si el archivo no existe o está vacío, lo inicializa con un array vacío.
    if (!file_exists(USERS_FILE) || filesize(USERS_FILE) === 0) {
        file_put_contents(USERS_FILE, json_encode([]));
    }
    $usersData = file_get_contents(USERS_FILE);
    return json_decode($usersData, true); // true para array asociativo
}

/**
 * Guarda el array de usuarios en el archivo JSON.
 * @param array $users El array de usuarios a guardar.
 */
function saveUsers($users) {
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
}

/**
 * Busca un usuario por nombre de usuario.
 * @param string $username El nombre de usuario a buscar.
 * @return array|null El array de usuario si se encuentra, o null.
 */
function findUserByUsername($username) {
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return $user;
        }
    }
    return null;
}

/**
 * Actualiza la nota personal de un usuario.
 * @param string $username El nombre de usuario.
 * @param string $newNote La nueva nota.
 * @return bool True si la nota se actualizó, false si el usuario no se encontró.
 */
function updateUserNote($username, $newNote) {
    $users = getUsers();
    $userFound = false;
    foreach ($users as &$user) { // Usar & para pasar por referencia y modificar el array original
        if ($user['username'] === $username) {
            $user['note'] = $newNote;
            $userFound = true;
            break;
        }
    }
    if ($userFound) {
        saveUsers($users);
        return true;
    }
    return false;
}

/**
 * Registra un nuevo usuario.
 * @param string $username El nombre de usuario.
 * @param string $password La contraseña (sin hash para esta demo, ¡no usar en producción!).
 * @return bool True si el registro es exitoso, false si el usuario ya existe.
 */
function registerNewUser($username, $password) {
    $users = getUsers();
    if (findUserByUsername($username)) {
        return false; // Usuario ya existe
    }
    // ADVERTENCIA DE SEGURIDAD: Nunca guardar contraseñas en texto plano en producción.
    // Usar password_hash() y password_verify().
    $users[] = ['username' => $username, 'password' => $password, 'note' => ''];
    saveUsers($users);
    return true;
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
 * Verifica si el usuario está autenticado.
 * @return array|null El objeto de usuario si está logueado, o null.
 */
function isAuthenticated() {
    if (isset($_SESSION['username'])) {
        return findUserByUsername($_SESSION['username']);
    }
    return null;
}
?>
