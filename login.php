<?php
session_start(); // Siempre inicia la sesión al principio
require_once 'functions.php'; // Incluye el archivo de funciones

$errorMessage = '';

// Si el usuario ya está logueado, redirige al dashboard
if (isAuthenticated()) {
    redirectTo('dashboard.php');
}

// Maneja el envío del formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $errorMessage = 'Por favor, introduce usuario y contraseña.';
    } else {
        $user = findUserByUsername($username);

        // ATENCIÓN: $user['password'] === $password es INSEGURO para producción.
        // Usar password_verify($password, $user['password_hash']) para contraseñas hasheadas.
        if ($user && $user['password'] === $password) {
            $_SESSION['username'] = $user['username']; // Establece la sesión
            redirectTo('dashboard.php'); // Redirige al dashboard
        } else {
            $errorMessage = 'Usuario o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DarkAbout - Iniciar Sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #121212; color: #eee; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
        .section-container { background-color: #1f1f1f; padding: 2.5rem; border-radius: 15px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5); width: 100%; max-width: 450px; text-align: center; margin-top: 20px; }
        h1 { color: #ff3f3f; font-size: 3rem; margin-bottom: 0.5rem; text-align: center; }
        h2 { color: #ff3f3f; font-size: 2rem; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; text-align: left; }
        label { display: block; margin-bottom: 0.5rem; color: #ccc; font-weight: bold; }
        input[type="text"], input[type="password"] { width: calc(100% - 20px); padding: 0.75rem 10px; border: 1px solid #444; border-radius: 8px; background-color: #333; color: #eee; font-size: 1rem; outline: none; transition: border-color 0.3s ease, box-shadow 0.3s ease; }
        input[type="text"]:focus, input[type="password"]:focus { border-color: #ff3f3f; box-shadow: 0 0 0 3px rgba(255, 63, 63, 0.3); }
        button { background-color: #ff3f3f; color: #fff; padding: 0.8rem 1.8rem; border: none; border-radius: 9999px; cursor: pointer; font-size: 1.1rem; font-weight: bold; transition: background-color 0.3s ease, transform 0.2s ease; margin: 0.5rem; outline: none; }
        button:hover { background-color: #e03030; transform: translateY(-2px); }
        button:active { transform: translateY(0); }
        .message { margin-top: 1rem; padding: 0.75rem; border-radius: 8px; font-weight: bold; opacity: 1; font-size: 0.95rem; }
        .message.success { background-color: #28a745; color: #fff; }
        .message.error { background-color: #dc3545; color: #fff; }
        @media (max-width: 600px) { h1 { font-size: 2.2rem; } .section-container { padding: 1.5rem; } button { padding: 0.7rem 1.5rem; font-size: 1rem; } }
    </style>
</head>
<body>
    <h1>Bienvenido a DarkAbout</h1>
    <p>Tu espacio online, sencillo y directo.</p>

    <div class="section-container">
        <h2 class="text-2xl font-bold text-white mb-6">Iniciar Sesión</h2>
        <?php if (!empty($errorMessage)): ?>
            <div class="message error" role="alert">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-4">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" name="login_submit">Entrar</button>
            <button type="button" onclick="window.location.href='register.php'">Registrarse</button>
        </form>
    </div>
</body>
</html>
