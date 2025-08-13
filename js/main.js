// js/main.js
// Importar funciones de los módulos auth.js y ui.js
import { getUsers, saveUsers, registerUser, loginUser } from './auth.js';
import { showUIMessage, copyToClipboard } from './ui.js';

// Asegurarse de que el DOM esté completamente cargado antes de ejecutar el script
document.addEventListener('DOMContentLoaded', () => {
    // --- Referencias a elementos del DOM ---
    const loginSection = document.getElementById('login-section');
    const registerSection = document.getElementById('register-section');
    const welcomeSection = document.getElementById('welcome-section');

    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    const loginUsernameInput = document.getElementById('login-username');
    const loginPasswordInput = document.getElementById('login-password');
    const registerUsernameInput = document.getElementById('register-username');
    const registerPasswordInput = document.getElementById('register-password');

    const loginMessage = document.getElementById('login-message');
    const registerMessage = document.getElementById('register-message');
    const copyMessage = document.getElementById('copy-message'); // From welcome section

    const welcomeUsernameSpan = document.getElementById('welcome-username');
    const contactLink = document.getElementById('contactLink');
    const logoutButton = document.getElementById('logout-button');
    const showRegisterButton = document.getElementById('show-register');
    const showLoginButton = document.getElementById('show-login');

    // --- Variables de estado de la aplicación ---
    let currentUser = null; // Almacena el usuario actualmente logueado

    // --- Funciones para manejar la visibilidad de las secciones ---
    /**
     * Oculta todas las secciones y luego muestra la sección deseada.
     * @param {string} sectionId - El ID de la sección a mostrar (e.g., 'login-section').
     */
    const showSection = (sectionId) => {
        const sections = [loginSection, registerSection, welcomeSection];
        sections.forEach(section => {
            section.classList.remove('active'); // Remove 'active' class from all
            section.classList.add('hidden'); // Add 'hidden' to hide (Tailwind)
        });

        const targetSection = document.getElementById(sectionId);
        if (targetSection) {
            targetSection.classList.remove('hidden'); // Remove 'hidden'
            // Small delay to ensure 'hidden' class is applied before adding 'active' for transition
            setTimeout(() => {
                targetSection.classList.add('active'); // Add 'active' for transition
            }, 10); 
        }
    };

    // --- Lógica de Registro ---
    registerForm.addEventListener('submit', (event) => {
        event.preventDefault(); // Prevent form submission
        const username = registerUsernameInput.value.trim();
        const password = registerPasswordInput.value.trim();

        if (username === '' || password === '') {
            showUIMessage(registerMessage, 'Por favor, rellena todos los campos.', false);
            return;
        }

        const newUser = registerUser(username, password); // Usa la función importada
        if (newUser) {
            showUIMessage(registerMessage, '¡Registro exitoso! Ya puedes iniciar sesión.', true);
            registerForm.reset(); // Clear the form
            setTimeout(() => showSection('login-section'), 2000); // Go back to login after message
        } else {
            showUIMessage(registerMessage, 'Este usuario ya existe.', false);
        }
    });

    // --- Lógica de Login ---
    loginForm.addEventListener('submit', (event) => {
        event.preventDefault(); // Prevent form submission
        const username = loginUsernameInput.value.trim();
        const password = loginPasswordInput.value.trim();

        if (username === '' || password === '' ) {
            showUIMessage(loginMessage, 'Por favor, introduce usuario y contraseña.', false);
            return;
        }

        const user = loginUser(username, password); // Usa la función importada
        if (user) {
            currentUser = user; // Set logged in user
            welcomeUsernameSpan.textContent = currentUser.username; // Display username
            showSection('welcome-section'); // Go to welcome section
            loginForm.reset(); // Clear the form
            showUIMessage(loginMessage, '¡Inicio de sesión exitoso!', true); // Opcional: mostrar mensaje aquí o no.
        } else {
            showUIMessage(loginMessage, 'Usuario o contraseña incorrectos.', false);
        }
    });

    // --- Lógica de Logout ---
    logoutButton.addEventListener('click', () => {
        currentUser = null; // Clear logged in user
        showSection('login-section'); // Go back to login section
    });

    // --- Manejo de la visibilidad de formularios ---
    showRegisterButton.addEventListener('click', () => {
        showSection('register-section');
        loginForm.reset(); // Clear login form when switching
        showUIMessage(loginMessage, '', false); // Hide previous messages
    });

    showLoginButton.addEventListener('click', () => {
        showSection('login-section');
        registerForm.reset(); // Clear register form when switching
        showUIMessage(registerMessage, '', false); // Hide previous messages
    });

    // --- Funcionalidad de Copiar Correo ---
    contactLink.addEventListener('click', (event) => {
        event.preventDefault(); // Prevent default behavior

        const emailAddress = contactLink.getAttribute('href').replace('mailto:', '');
        const success = copyToClipboard(emailAddress); // Usa la función importada

        const msg = success ? '¡Correo copiado!' : 'Error al copiar el correo.';
        showUIMessage(copyMessage, msg, success);
    });

    // --- Inicialización de la aplicación ---
    // Al cargar la página, siempre mostramos la sección de login inicialmente
    showSection('login-section');
});
