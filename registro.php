<?php
require_once 'config/database.php';
require_once 'includes/session.php';

// Si el usuario ya está logueado, redirigir según su rol
if (isLoggedIn()) {
    redirectBasedOnRole();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validaciones
    if (empty($nombre) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Todos los campos son obligatorios";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden";
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres";
    } else {
        // Verificar si el email ya existe
        $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Este correo electrónico ya está registrado";
        } else {
            // Crear el usuario
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'cliente')");
            
            if ($stmt->execute([$nombre, $email, $hashed_password])) {
                $success = "Registro exitoso. Ahora puedes iniciar sesión.";
            } else {
                $error = "Error al registrar el usuario";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sweet Mett</title>
    <link href="css/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bg-chocolate-darker { background-color: #3A2314; }
        .hover\:bg-chocolate-dark:hover { background-color: #4A2D1A; }
        .video-bg {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            object-fit: cover;
            z-index: 0;
        }
    </style>
</head>
<body class="relative min-h-screen flex items-center justify-center">
    <!-- Video de fondo -->
    <video class="video-bg" src="assets/video1.mp4" autoplay loop muted playsinline></video>
    <!-- Overlay oscuro y desenfocado -->
    <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(40,20,10,0.55);backdrop-filter:blur(2px);z-index:1;"></div>
    <!-- Formulario centrado -->
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="position:relative;z-index:2;">
        <div class="max-w-4xl w-full space-y-8 bg-white bg-opacity-90 rounded-xl shadow-lg p-8">
            <div class="text-center">
                <a href="index.php">
                    <img src="assets/logo-blanco.png" alt="Logo" class="mx-auto h-24 w-auto">
                </a>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Crear Cuenta
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    ¿Ya tienes una cuenta?
                    <a href="login.php" class="font-medium text-chocolate-darker hover:text-chocolate-dark">
                        Inicia sesión aquí
                    </a>
                </p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" method="POST">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="nombre" class="sr-only">Nombre</label>
                        <input id="nombre" name="nombre" type="text" required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-chocolate-dark focus:border-chocolate-dark focus:z-10 sm:text-sm"
                            placeholder="Nombre completo">
                    </div>
                    <div>
                        <label for="email" class="sr-only">Correo electrónico</label>
                        <input id="email" name="email" type="email" required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-chocolate-dark focus:border-chocolate-dark focus:z-10 sm:text-sm"
                            placeholder="Correo electrónico">
                    </div>
                    <div>
                        <label for="password" class="sr-only">Contraseña</label>
                        <input id="password" name="password" type="password" required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-chocolate-dark focus:border-chocolate-dark focus:z-10 sm:text-sm"
                            placeholder="Contraseña">
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-chocolate-darker hover:bg-chocolate-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-chocolate-dark">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-user-plus"></i>
                        </span>
                        Registrarse
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <a href="index.php" class="font-medium text-chocolate-darker hover:text-chocolate-dark">
                    <i class="fas fa-home mr-2"></i>Volver al inicio
                </a>
            </div>
        </div>
    </div>
</body>
</html>