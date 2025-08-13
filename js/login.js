// js/login.js
// Importa las funciones necesarias desde los módulos auth.js y ui.js
import { loginUser, setSession } from './auth.js';
import { showUIMessage } from './ui.js';

// Asegura que el código se ejecute una vez que todo el DOM (Document Object Model) esté cargado
document.addEventListener('DOMContentLoaded', () => {
    // Obtiene referencias a los elementos HTML del formulario de login
    const loginForm = document.getElementById('login-form');
    const loginUsernameInput = document.getElementById('login-username');
    const loginPasswordInput = document.getElementById('login-password');
    const loginMessage = document.getElementById('login-message'); // Elemento para mostrar mensajes al usuario
    const showRegisterButton = document.getElementById('show-register'); // Botón para ir a la página de registro

    // Añade un "event listener" al botón de "Registrarse"
    // Cuando se hace clic, redirige al usuario a la página de registro (register.html)
    showRegisterButton.addEventListener('click', () => {
        window.location.href = 'register.html';
    });

    // Añade un "event listener" para el evento de envío (submit) del formulario de login
    loginForm.addEventListener('submit', (event) => {
        event.preventDefault(); // Previene el comportamiento por defecto del formulario (recargar la página)

        // Obtiene los valores de usuario y contraseña de los campos de entrada, eliminando espacios en blanco al inicio/final
        const username = loginUsernameInput.value.trim();
        const password = loginPasswordInput.value.trim();

        // Validación básica: comprueba si los campos están vacíos
        if (username === '' || password === '') {
            // Muestra un mensaje de error si algún campo está vacío
            showUIMessage(loginMessage, 'Por favor, introduce usuario y contraseña.', false);
            return; // Detiene la ejecución de la función
        }

        // Intenta iniciar sesión utilizando la función `loginUser` importada de `auth.js`
        const user = loginUser(username, password);

        // Verifica si el inicio de sesión fue exitoso
        if (user) {
            // Si el usuario existe y las credenciales son correctas:
            setSession(user.username); // Guarda el nombre de usuario en el localStorage para mantener la sesión
            showUIMessage(loginMessage, '¡Inicio de sesión exitoso! Redirigiendo...', true); // Muestra un mensaje de éxito
            
            // Redirige al usuario a la página del dashboard (dashboard.html) después de un breve retraso
            setTimeout(() => {
                window.location.href = 'dashboard.html';
            }, 1500); // Retraso de 1.5 segundos para que el usuario pueda leer el mensaje
        } else {
            // Si el usuario no se encuentra o las credenciales son incorrectas:
            showUIMessage(loginMessage, 'Usuario o contraseña incorrectos.', false); // Muestra un mensaje de error
        }
    });
});
