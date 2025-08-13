// js/dashboard.js
import { getSession, clearSession, findUserByUsername, updateUserNote } from './auth.js';
import { showUIMessage, copyToClipboard } from './ui.js';

document.addEventListener('DOMContentLoaded', () => {
    const welcomeUsernameSpan = document.getElementById('welcome-username');
    const userNoteInput = document.getElementById('user-note-input');
    const saveNoteButton = document.getElementById('save-note-button');
    const displayNote = document.getElementById('display-note');
    const dashboardMessage = document.getElementById('dashboard-message');
    const contactLink = document.getElementById('contactLink');
    const copyMessage = document.getElementById('copy-message');
    const logoutButton = document.getElementById('logout-button');

    let currentUser = null; // Variable para almacenar los datos del usuario logueado

    /**
     * Carga la nota del usuario actual en el área de texto y la muestra.
     */
    const loadUserNote = () => {
        if (currentUser && currentUser.note !== undefined) {
            userNoteInput.value = currentUser.note;
            displayNote.textContent = currentUser.note;
        } else {
            userNoteInput.value = '';
            displayNote.textContent = 'No hay nota guardada.';
        }
    };

    // --- Lógica de verificación de sesión al cargar la página ---
    const sessionUsername = getSession();
    if (sessionUsername) {
        currentUser = findUserByUsername(sessionUsername);
        if (currentUser) {
            welcomeUsernameSpan.textContent = currentUser.username;
            loadUserNote(); // Cargar la nota del usuario si hay sesión activa
        } else {
            // Usuario en sesión no encontrado en la DB (posiblemente borrado o inconsistencia)
            clearSession(); // Limpiar sesión
            window.location.href = 'login.html'; // Redirigir al login
        }
    } else {
        // No hay sesión activa, redirigir al login
        window.location.href = 'login.html';
    }

    // --- Lógica de Guardar Nota en Dashboard ---
    saveNoteButton.addEventListener('click', () => {
        if (currentUser) {
            const newNote = userNoteInput.value.trim();
            const updatedUser = updateUserNote(currentUser.username, newNote); // Usa la función importada

            if (updatedUser) {
                currentUser = updatedUser; // Actualizar el usuario actual con la nota guardada
                displayNote.textContent = currentUser.note; // Actualizar la nota mostrada
                showUIMessage(dashboardMessage, '¡Nota guardada exitosamente!', true);
            } else {
                showUIMessage(dashboardMessage, 'Error al guardar la nota. Usuario no encontrado.', false);
            }
        } else {
            showUIMessage(dashboardMessage, 'No has iniciado sesión.', false);
            // Esto no debería ocurrir si la verificación inicial funciona correctamente, pero es un fallback
            clearSession();
            window.location.href = 'login.html';
        }
    });

    // --- Lógica de Logout ---
    logoutButton.addEventListener('click', () => {
        clearSession(); // Limpiar la sesión de localStorage
        currentUser = null; // Limpiar el usuario actual en memoria
        userNoteInput.value = ''; // Limpiar campo de nota
        displayNote.textContent = ''; // Limpiar nota mostrada
        window.location.href = 'login.html'; // Redirigir al login
    });

    // --- Funcionalidad de Copiar Correo ---
    contactLink.addEventListener('click', (event) => {
        event.preventDefault();

        const emailAddress = contactLink.getAttribute('href').replace('mailto:', '');
        const success = copyToClipboard(emailAddress);

        const msg = success ? '¡Correo copiado!' : 'Error al copiar el correo.';
        showUIMessage(copyMessage, msg, success);
    });
});
