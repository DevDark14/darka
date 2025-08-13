// js/dashboard.js
// Importa las funciones necesarias desde los módulos auth.js y ui.js
import { getSession, clearSession, findUserByUsername, updateUserNote } from './auth.js';
import { showUIMessage, copyToClipboard } from './ui.js';

// Asegura que el código se ejecute una vez que todo el DOM esté cargado
document.addEventListener('DOMContentLoaded', () => {
    // Obtiene referencias a los elementos HTML del dashboard
    const welcomeUsernameSpan = document.getElementById('welcome-username');
    const userNoteInput = document.getElementById('user-note-input');
    const saveNoteButton = document.getElementById('save-note-button');
    const displayNote = document.getElementById('display-note');
    const dashboardMessage = document.getElementById('dashboard-message'); // Elemento para mensajes en el dashboard
    const contactLink = document.getElementById('contactLink');
    const copyMessage = document.getElementById('copy-message'); // Elemento para mensajes de copiar correo
    const logoutButton = document.getElementById('logout-button');

    let currentUser = null; // Variable para almacenar los datos del usuario logueado en la sesión actual

    /**
     * Carga la nota personal del usuario actual en el textarea y en el div de visualización.
     */
    const loadUserNote = () => {
        if (currentUser && currentUser.note !== undefined) {
            userNoteInput.value = currentUser.note; // Carga la nota en el campo de edición
            displayNote.textContent = currentUser.note; // Muestra la nota en el área de visualización
        } else {
            userNoteInput.value = ''; // Limpia el campo si no hay nota
            displayNote.textContent = 'No hay nota guardada.'; // Mensaje predeterminado
        }
    };

    // --- Lógica de verificación y carga de sesión al cargar la página ---
    const sessionUsername = getSession(); // Intenta obtener el nombre de usuario de la sesión guardada

    if (sessionUsername) {
        // Si hay un nombre de usuario en la sesión, intenta encontrar sus datos completos
        currentUser = findUserByUsername(sessionUsername);

        if (currentUser) {
            // Si el usuario existe, actualiza la UI del dashboard con sus datos
            welcomeUsernameSpan.textContent = currentUser.username; // Muestra el nombre de usuario
            loadUserNote(); // Carga la nota personal del usuario
        } else {
            // Si el nombre de usuario de la sesión no corresponde a un usuario existente,
            // la sesión es inválida. Se limpia y se redirige al login.
            console.warn('Usuario de sesión no encontrado en la base de datos. Limpiando sesión...');
            clearSession(); // Limpia la sesión inválida
            window.location.href = 'login.html'; // Redirige al login
        }
    } else {
        // Si no hay ningún nombre de usuario guardado en la sesión, el usuario no está logueado.
        // Se le redirige inmediatamente a la página de login.
        console.log('No hay sesión activa. Redirigiendo a login...');
        window.location.href = 'login.html'; // Redirige al login
    }

    // --- Lógica para guardar la nota personal del usuario ---
    saveNoteButton.addEventListener('click', () => {
        if (currentUser) {
            // Si hay un usuario logueado, obtener la nueva nota
            const newNote = userNoteInput.value.trim();
            // Actualizar la nota del usuario en la "base de datos" (localStorage)
            const updatedUser = updateUserNote(currentUser.username, newNote);

            if (updatedUser) {
                // Si la actualización fue exitosa, actualiza el objeto currentUser en memoria
                currentUser = updatedUser;
                // Muestra la nota actualizada en el área de visualización
                displayNote.textContent = currentUser.note;
                // Muestra un mensaje de éxito al usuario
                showUIMessage(dashboardMessage, '¡Nota guardada exitosamente!', true);
            } else {
                // Muestra un mensaje de error si la actualización falla (ej. usuario no encontrado)
                showUIMessage(dashboardMessage, 'Error al guardar la nota. Usuario no encontrado.', false);
            }
        } else {
            // Si por alguna razón no hay un currentUser, muestra un mensaje y redirige al login
            showUIMessage(dashboardMessage, 'No has iniciado sesión.', false);
            console.error('Intento de guardar nota sin sesión activa. Redirigiendo...');
            clearSession();
            window.location.href = 'login.html';
        }
    });

    // --- Lógica para cerrar sesión ---
    logoutButton.addEventListener('click', () => {
        clearSession(); // Limpia la sesión de localStorage
        currentUser = null; // Limpia el usuario actual en memoria
        userNoteInput.value = ''; // Borra el contenido del campo de nota
        displayNote.textContent = ''; // Borra la nota mostrada
        window.location.href = 'login.html'; // Redirige al usuario a la página de login
    });

    // --- Funcionalidad para copiar el correo electrónico ---
    contactLink.addEventListener('click', (event) => {
        event.preventDefault(); // Previene el comportamiento por defecto del enlace

        const emailAddress = contactLink.getAttribute('href').replace('mailto:', '');
        const success = copyToClipboard(emailAddress); // Intenta copiar el correo

        const msg = success ? '¡Correo copiado!' : 'Error al copiar el correo.';
        showUIMessage(copyMessage, msg, success); // Muestra el mensaje de éxito o error
    });
});
