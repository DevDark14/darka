<?php
session_start(); // Siempre inicia la sesión al principio

// Incluye el archivo de funciones desde la nueva ubicación en la carpeta 'core'.
// __DIR__ se refiere al directorio actual (public/), y ../ sube un nivel para ir a la raíz del proyecto,
// y luego entra en 'core/functions.php'.
require_once __DIR__ . '/../core/functions.php';

$currentUser = isAuthenticated();
$dashboardMessage = '';

// Si no hay usuario autenticado, redirige al login
if (!$currentUser) {
    redirectTo('login.php');
}

// Maneja el envío del formulario de la nota
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_note_submit'])) {
    $newNote = trim($_POST['user_note'] ?? '');
    if (updateUserNote($currentUser['username'], $newNote)) {
        // Vuelve a cargar el usuario para obtener la nota actualizada
        $currentUser = findUserByUsername($currentUser['username']);
        $dashboardMessage = '¡Nota guardada exitosamente!';
        $isSuccessDashboardMessage = true;
    } else {
        $dashboardMessage = 'Error al guardar la nota.';
        $isSuccessDashboardMessage = false;
    }
}

// Lógica para cerrar sesión
if (isset($_GET['logout'])) {
    session_unset();    // Elimina todas las variables de sesión
    session_destroy();  // Destruye la sesión
    redirectTo('login.php'); // Redirige al login.php, que también está en 'public/'
}

// Configura la nota a mostrar
$displayNote = $currentUser['note'] ?? 'No hay nota guardada.';
$userNoteInputValue = $currentUser['note'] ?? ''; // Valor para el textarea
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DarkAbout - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #121212; color: #eee; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
        .section-container { background-color: #1f1f1f; padding: 2.5rem; border-radius: 15px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5); width: 100%; max-width: 450px; text-align: center; margin-top: 20px; }
        h1 { color: #ff3f3f; font-size: 3rem; margin-bottom: 0.5rem; text-align: center; }
        h2 { color: #ff3f3f; font-size: 2rem; margin-bottom: 1.5rem; }
        p { font-size: 1.3rem; color: #bbb; text-align: center; margin-bottom: 1.5rem; }
        .note-section { background-color: #2a2a2a; padding: 1.5rem; border-radius: 10px; margin-top: 1.5rem; text-align: left; }
        .note-section h3 { color: #ff3f3f; margin-bottom: 1rem; font-size: 1.4rem; }
        #display-note { background-color: #333; color: #eee; padding: 1rem; border-radius: 8px; min-height: 100px; white-space: pre-wrap; word-wrap: break-word; margin-bottom: 1rem; }
        textarea { width: calc(100% - 20px); padding: 0.75rem 10px; border: 1px solid #444; border-radius: 8px; background-color: #333; color: #eee; font-size: 1rem; outline: none; transition: border-color 0.3s ease, box-shadow 0.3s ease; resize: vertical; min-height: 80px; }
        textarea:focus { border-color: #ff3f3f; box-shadow: 0 0 0 3px rgba(255, 63, 63, 0.3); }
        button { background-color: #ff3f3f; color: #fff; padding: 0.8rem 1.8rem; border: none; border-radius: 9999px; cursor: pointer; font-size: 1.1rem; font-weight: bold; transition: background-color 0.3s ease, transform 0.2s ease; margin: 0.5rem; outline: none; }
        button:hover { background-color: #e03030; transform: translateY(-2px); }
        button:active { transform: translateY(0); }
        .message { margin-top: 1rem; padding: 0.75rem; border-radius: 8px; font-weight: bold; opacity: 1; font-size: 0.95rem; }
        .message.success { background-color: #28a745; color: #fff; }
        .message.error { background-color: #dc3545; color: #fff; }
        #contactLink { color: #ff3f3f; text-decoration: none; font-weight: bold; border: 2px solid #ff3f3f; padding: 0.75rem 1.5rem; border-radius: 9999px; transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease; display: inline-block; margin-top: 1.5rem; }
        #contactLink:hover { background-color: #ff3f3f; color: #121212; transform: translateY(-2px); }
        #copy-message { margin-top: 1rem; color: #4CAF50; font-weight: bold; opacity: 0; transition: opacity 0.5s ease-in-out; font-size: 1rem; }
        #copy-message.show { opacity: 1; }
        @media (max-width: 600px) { h1 { font-size: 2.2rem; } p { font-size: 1rem; } .section-container { padding: 1.5rem; } button { padding: 0.7rem 1.5rem; font-size: 1rem; } }
    </style>
</head>
<body>
    <h1>Bienvenido a DarkAbout</h1>
    <p>Tu espacio online, sencillo y directo.</p>

    <div class="section-container">
        <h2 class="text-2xl font-bold text-white mb-6">¡Hola, <?php echo htmlspecialchars($currentUser['username']); ?>!</h2>
        <p>Bienvenido de nuevo a DarkAbout.</p>

        <div class="note-section">
            <h3>Tu Nota Personal</h3>
            <div id="display-note"><?php echo htmlspecialchars($displayNote); ?></div>
            <form action="dashboard.php" method="POST">
                <textarea id="user-note-input" name="user_note" placeholder="Escribe tu nota aquí..." class="mt-4"><?php echo htmlspecialchars($userNoteInputValue); ?></textarea>
                <button type="submit" name="save_note_submit" class="mt-4">Guardar Nota</button>
            </form>
            <?php if (!empty($dashboardMessage)): ?>
                <div class="message <?php echo $isSuccessDashboardMessage ? 'success' : 'error'; ?>" role="alert">
                    <?php echo htmlspecialchars($dashboardMessage); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- El enlace con el correo electrónico (requiere JS para copiar) -->
        <a id="contactLink" href="mailto:contacto@darkabout.xyz" class="mt-8">Contáctanos</a>
        <span id="copy-message"></span>

        <button type="button" onclick="window.location.href='dashboard.php?logout=true'" class="mt-8">Cerrar Sesión</button>
    </div>

    <!-- JavaScript para funcionalidad de cliente (ej. copiar al portapapeles) -->
    <script>
        // Funcionalidad de copiar al portapapeles para el enlace de contacto
        document.addEventListener('DOMContentLoaded', () => {
            const contactLink = document.getElementById('contactLink');
            const copyMessageSpan = document.getElementById('copy-message');

            if (contactLink && copyMessageSpan) {
                contactLink.addEventListener('click', (event) => {
                    event.preventDefault(); // Prevenir el comportamiento por defecto del enlace

                    const emailAddress = contactLink.getAttribute('href').replace('mailto:', '');

                    // Intenta usar la API moderna del portapapeles
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(emailAddress)
                            .then(() => {
                                copyMessageSpan.textContent = '¡Correo copiado!';
                                copyMessageSpan.classList.add('show');
                                setTimeout(() => { copyMessageSpan.classList.remove('show'); }, 3000);
                            })
                            .catch(err => {
                                console.error('Error al copiar (API Clipboard): ', err);
                                copyMessageSpan.textContent = 'Error al copiar el correo.';
                                copyMessageSpan.classList.add('show');
                                setTimeout(() => { copyMessageSpan.classList.remove('show'); }, 3000);
                            });
                    } else {
                        // Fallback para navegadores antiguos o entornos sin navigator.clipboard (como iframes)
                        const tempInput = document.createElement('textarea');
                        tempInput.value = emailAddress;
                        document.body.appendChild(tempInput);
                        tempInput.select();
                        tempInput.setSelectionRange(0, 99999); /* Para dispositivos móviles */

                        try {
                            const successful = document.execCommand('copy');
                            document.body.removeChild(tempInput);
                            if (successful) {
                                copyMessageSpan.textContent = '¡Correo copiado!';
                                copyMessageSpan.classList.add('show');
                            } else {
                                copyMessageSpan.textContent = 'Error al copiar el correo.';
                                copyMessageSpan.classList.add('show');
                            }
                        } catch (err) {
                            console.error('Error al copiar (execCommand): ', err);
                            copyMessageSpan.textContent = 'Error al copiar el correo.';
                            copyMessageSpan.classList.add('show');
                        } finally {
                            document.body.removeChild(tempInput);
                            setTimeout(() => { copyMessageSpan.classList.remove('show'); }, 3000);
                        }
                    }
                });
            }

            // Script para que los mensajes de PHP tengan un fade-out
            const phpMessages = document.querySelectorAll('.message');
            phpMessages.forEach(msg => {
                // Solo para mensajes que no están ya ocultos por PHP
                if (msg.textContent.trim() !== '') {
                    setTimeout(() => {
                        msg.style.opacity = '0';
                        setTimeout(() => { msg.style.display = 'none'; }, 500); // Espera la transición antes de ocultar
                    }, 3000);
                }
            });
        });
    </script>
</body>
</html>
