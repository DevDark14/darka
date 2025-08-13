// Asegurarse de que el DOM esté completamente cargado antes de ejecutar el script
document.addEventListener('DOMContentLoaded', () => {
    // Obtener referencias a los elementos del DOM
    const contactLink = document.getElementById('contactLink');
    const copyMessage = document.getElementById('copy-message');

    // Añadir un "event listener" al enlace de contacto
    contactLink.addEventListener('click', (event) => {
        // Prevenir el comportamiento por defecto del enlace (abrir el cliente de correo)
        event.preventDefault();

        // Obtener el correo electrónico del atributo href del enlace
        const emailAddress = contactLink.getAttribute('href').replace('mailto:', '');

        // Crear un elemento de texto temporal para copiar el contenido al portapapeles
        const tempInput = document.createElement('textarea');
        tempInput.value = emailAddress;
        document.body.appendChild(tempInput);
        tempInput.select(); // Seleccionar el texto dentro del textarea
        tempInput.setSelectionRange(0, 99999); /* Para dispositivos móviles */

        try {
            // Ejecutar el comando de copia al portapapeles
            // document.execCommand('copy') es una API legacy, pero es más compatible en entornos de iframe.
            const successful = document.execCommand('copy');
            const msg = successful ? '¡Correo copiado!' : 'Error al copiar el correo.';
            
            // Mostrar el mensaje de confirmación
            copyMessage.textContent = msg;
            copyMessage.classList.add('show'); // Añadir clase para hacer visible el mensaje

            // Ocultar el mensaje después de 3 segundos
            setTimeout(() => {
                copyMessage.classList.remove('show'); // Quitar clase para ocultar el mensaje
                copyMessage.textContent = ''; // Limpiar el texto
            }, 3000);
        } catch (err) {
            console.error('No se pudo copiar el texto: ', err);
            copyMessage.textContent = 'Error al copiar el correo.';
            copyMessage.classList.add('show');
            setTimeout(() => {
                copyMessage.classList.remove('show');
                copyMessage.textContent = '';
            }, 3000);
        } finally {
            // Eliminar el elemento temporal del DOM
            document.body.removeChild(tempInput);
        }
    });
});
