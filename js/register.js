// js/register.js
import { registerUser } from './auth.js';
import { showUIMessage } from './ui.js';

document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('register-form');
    const registerUsernameInput = document.getElementById('register-username');
    const registerPasswordInput = document.getElementById('register-password');
    const registerMessage = document.getElementById('register-message');
    const showLoginButton = document.getElementById('show-login');

    // Redirigir a login.html
    showLoginButton.addEventListener('click', () => {
        window.location.href = 'login.html';
    });

    registerForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const username = registerUsernameInput.value.trim();
        const password = registerPasswordInput.value.trim();

        if (username === '' || password === '') {
            showUIMessage(registerMessage, 'Por favor, rellena todos los campos.', false);
            return;
        }

        const newUser = registerUser(username, password);
        if (newUser) {
            showUIMessage(registerMessage, '¡Registro exitoso! Ya puedes iniciar sesión.', true);
            registerForm.reset();
            // Redirigir a la página de login
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000); // Dar tiempo para que el mensaje se vea
        } else {
            showUIMessage(registerMessage, 'Este usuario ya existe.', false);
        }
    });
});
