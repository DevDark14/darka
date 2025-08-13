// js/auth.js

/**
 * Carga los usuarios desde localStorage.
 * @returns {Array} Un array de objetos de usuario. Si no hay usuarios, devuelve un array vacío.
 */
export const getUsers = () => {
    try {
        const usersJson = localStorage.getItem('users');
        return usersJson ? JSON.parse(usersJson) : [];
    } catch (e) {
        console.error('Error loading users from localStorage:', e);
        return [];
    }
};

/**
 * Guarda el array de usuarios en localStorage.
 * @param {Array} users - El array de objetos de usuario a guardar.
 */
export const saveUsers = (users) => {
    try {
        localStorage.setItem('users', JSON.stringify(users));
    } catch (e) {
        console.error('Error saving users to localStorage:', e);
    }
};

/**
 * Registra un nuevo usuario en la "base de datos" (localStorage).
 * @param {string} username - El nombre de usuario a registrar.
 * @param {string} password - La contraseña del usuario (ATENCIÓN: NO SEGURO PARA PRODUCCIÓN).
 * @returns {object|null} El objeto de usuario si el registro es exitoso, o null si el usuario ya existe.
 */
export const registerUser = (username, password) => {
    const users = getUsers();
    const userExists = users.some(user => user.username === username);

    if (userExists) {
        return null; // Usuario ya existe
    }

    // ADVERTENCIA DE SEGURIDAD: En una aplicación real, las contraseñas DEBEN ser hasheadas y salteadas.
    // NUNCA las almacenes en texto plano como aquí.
    const newUser = { username: username, password: password };
    users.push(newUser);
    saveUsers(users);
    return newUser;
};

/**
 * Intenta iniciar sesión con las credenciales proporcionadas.
 * @param {string} username - El nombre de usuario.
 * @param {string} password - La contraseña.
 * @returns {object|null} El objeto de usuario si las credenciales son válidas, o null si no.
 */
export const loginUser = (username, password) => {
    const users = getUsers();
    // Buscar usuario por nombre y contraseña (texto plano en esta demo)
    const foundUser = users.find(user => user.username === username && user.password === password);
    return foundUser || null;
};
