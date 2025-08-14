<?php
session_start(); // Siempre inicia la sesión al principio

// Incluye el archivo de funciones desde la nueva ubicación en la carpeta 'core'.
// __DIR__ se refiere al directorio actual (public/), y ../ sube un nivel para ir a la raíz del proyecto,
// y luego entra en 'core/functions.php'.
require_once __DIR__ . '/../core/functions.php';

$loginMessage = '';
$isSuccessLoginMessage = false;

// Si el usuario ya está autenticado, redirige al dashboard para evitar que vea el login.
if (isAuthenticated()) {
    redirectTo('dashboard.php');
}

// Maneja el envío del formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? ''; // La contraseña no se limpia para que password_verify la maneje (en producción)

    $user = findUserByUsername($username);

    // ADVERTENCIA DE SEGURIDAD: En producción, aquí harías:
    // if ($user && password_verify($password, $user['password'])) { ... }
    // Para esta demo, se compara directamente la contraseña (no segura para producción).
    if ($user && $user['password'] === $password) {
        $_SESSION['username'] = $user['username'];
        redirectTo('dashboard.php'); // Redirige al dashboard tras un login exitoso
    } else {
        $loginMessage = 'Nombre de usuario o contraseña incorrectos.';
        $isSuccessLoginMessage = false;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DarkAbout - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #121212; color: #eee; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
        .section-container { background-color: #1f1f1f; padding: 2.5rem; border-radius: 15px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5); width: 100%; max-width: 450px; text-align: center; }
        h1 { color: #ff3f3f; font-size: 3rem; margin-bottom: 0.5rem; }
        h2 { color: #ff3f3f; font-size: 2rem; margin-bottom: 1.5rem; }
        p { font-size: 1.1rem; color: #bbb; margin-bottom: 1.5rem; }
        input[type="text"], input[type="password"] { width: calc(100% - 20px); padding: 0.75rem 10px; margin-bottom: 1rem; border: 1px solid #444; border-radius: 8px; background-color: #333; color: #eee; font-size: 1rem; outline: none; transition: border-color 0.3s ease, box-shadow 0.3s ease; }
        input[type="text"]:focus, input[type="password"]:focus { border-color: #ff3f3f; box-shadow: 0 0 0 3px rgba(255, 63, 63, 0.3); }
        button { background-color: #ff3f3f; color: #fff; padding: 0.8rem 1.8rem; border: none; border-radius: 9999px; cursor: pointer; font-size: 1.1rem; font-weight: bold; transition: background-color 0.3s ease, transform 0.2s ease; margin: 0.5rem; outline: none; }
        button:hover { background-color: #e03030; transform: translateY(-2px); }
        button:active { transform: translateY(0); }
        .message { margin-top: 1rem; padding: 0.75rem; border-radius: 8px; font-weight: bold; opacity: 1; font-size: 0.95rem; }
        .message.success { background-color: #28a745; color: #fff; }
        .message.error { background-color: #dc3545; color: #fff; }
        a { color: #ff3f3f; text-decoration: none; font-weight: bold; transition: color 0.2s ease; }
        a:hover { text-decoration: underline; }
        @media (max-width: 600px) { h1 { font-size: 2.2rem; } p { font-size: 1rem; } .section-container { padding: 1.5rem; } button { padding: 0.7rem 1.5rem; font-size: 1rem; } }
    </style>
</head>
<body>
    <h1>Bienvenido a DarkAbout</h1>
    <p>Inicia sesión o regístrate para continuar.</p>

    <div class="section-container">
        <h2 class="text-2xl font-bold text-white mb-6">Iniciar Sesión</h2>

        <form action="login.php" method="POST">
            <input type="text" id="username" name="username" placeholder="Nombre de usuario" required /><br />
            <input type="password" id="password" name="password" placeholder="Contraseña" required /><br />
            <button type="submit" name="login_submit">Iniciar Sesión</button>
        </form>

        <?php if (!empty($loginMessage)): ?>
            <div class="message <?php echo $isSuccessLoginMessage ? 'success' : 'error'; ?>" role="alert">
                <?php echo htmlspecialchars($loginMessage); ?>
            </div>
        <?php endif; ?>

        <p class="mt-4">¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
    </div>

    <!-- JavaScript para que los mensajes de PHP tengan un fade-out -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const phpMessages = document.querySelectorAll('.message');
            phpMessages.forEach(msg => {
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
