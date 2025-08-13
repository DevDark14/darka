// js/ui.js

/**
 * Muestra un mensaje temporal en la UI.
 * @param {HTMLElement} element - El elemento donde se mostrará el mensaje.
 * @param {string} msg - El texto del mensaje.
 * @param {boolean} isSuccess - True si es un mensaje de éxito, false para error.
 */
export const showUIMessage = (element, msg, isSuccess) => {
    element.textContent = msg;
    element.classList.remove('success', 'error'); // Clean previous classes
    element.classList.add(isSuccess ? 'success' : 'error', 'show'); // Add type class and show
    element.style.visibility = 'visible'; // Ensure visibility

    setTimeout(() => {
        element.classList.remove('show');
        element.style.visibility = 'hidden'; // Hide after transition
        element.textContent = ''; // Clear text
    }, 3000); // Message disappears after 3 seconds
};

/**
 * Copia el texto dado al portapapeles del usuario.
 * @param {string} textToCopy - El texto que se va a copiar.
 * @returns {boolean} True si la copia fue exitosa, false en caso contrario.
 */
export const copyToClipboard = (textToCopy) => {
    const tempInput = document.createElement('textarea');
    tempInput.value = textToCopy;
    document.body.appendChild(tempInput);
    tempInput.select();
    tempInput.setSelectionRange(0, 99999); /* For mobile devices */

    try {
        const successful = document.execCommand('copy');
        document.body.removeChild(tempInput);
        return successful;
    } catch (err) {
        console.error('Could not copy text: ', err);
        document.body.removeChild(tempInput);
        return false;
    }
};
