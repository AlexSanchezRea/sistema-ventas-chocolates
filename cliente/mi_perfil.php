<?php
require_once '../config/database.php';
require_once '../includes/session.php';

// Verificar si el usuario está logueado como cliente
if (!isClient()) {
    header('Location: ../login.php');
    exit();
}

$userId = getCurrentUserId();
$conn = getConnection();

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $password = trim($_POST['password']);
    $new_password = trim($_POST['new_password']);
    
    $errors = [];
    
    // Validar campos obligatorios
    if (empty($nombre)) {
        $errors[] = "El nombre es obligatorio";
    }
    if (empty($email)) {
        $errors[] = "El email es obligatorio";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El email no es válido";
    }
    
    // Verificar si el email ya existe (excluyendo el usuario actual)
    if (!empty($email)) {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmt->execute([$email, $userId]);
        if ($stmt->fetch()) {
            $errors[] = "El email ya está registrado";
        }
    }
    
    // Si se quiere cambiar la contraseña
    if (!empty($new_password)) {
        if (empty($password)) {
            $errors[] = "Debes proporcionar tu contraseña actual para cambiarla";
        } else {
            // Verificar contraseña actual
            $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!password_verify($password, $user['password'])) {
                $errors[] = "La contraseña actual no es correcta";
            }
        }
    }
    
    // Si no hay errores, actualizar perfil
    if (empty($errors)) {
        try {
            $conn->beginTransaction();
            
            // Actualizar datos básicos
            $stmt = $conn->prepare("
                UPDATE usuarios 
                SET nombre = ?, email = ?, telefono = ?, direccion = ?
                WHERE id = ?
            ");
            $stmt->execute([$nombre, $email, $telefono, $direccion, $userId]);
            
            // Si hay nueva contraseña, actualizarla
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $userId]);
            }
            
            $conn->commit();
            $success = "Perfil actualizado correctamente";
            
            // Actualizar la sesión
            $_SESSION['nombre'] = $nombre;
            
        } catch (PDOException $e) {
            $conn->rollBack();
            $errors[] = "Error al actualizar el perfil";
        }
    }
}

// Obtener datos actuales del usuario
$stmt = $conn->prepare("SELECT nombre, email, telefono, direccion FROM usuarios WHERE id = ?");
$stmt->execute([$userId]);
$usuario = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Sweet Mett</title>
    <link rel="stylesheet" href="../css/tailwind.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="bg-amber-50">
    <nav class="bg-chocolate-darker text-white py-4">
        <div class="container mx-auto px-4">
            <a href="mi_cuenta.php" class="text-2xl font-bold">Sweet Mett</a>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-chocolate-darker mb-8">Mi Perfil</h1>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="bg-white shadow-md rounded-lg p-6">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">
                        Nombre
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           id="nombre"
                           name="nombre"
                           type="text"
                           value="<?php echo htmlspecialchars($usuario['nombre']); ?>"
                           required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           id="email"
                           name="email"
                           type="email"
                           value="<?php echo htmlspecialchars($usuario['email']); ?>"
                           required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="telefono">
                        Teléfono
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           id="telefono"
                           name="telefono"
                           type="tel"
                           value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="direccion">
                        Dirección
                    </label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                              id="direccion"
                              name="direccion"
                              rows="3"><?php echo htmlspecialchars($usuario['direccion']); ?></textarea>
                </div>

                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-chocolate-darker mb-4">Cambiar Contraseña</h3>
                    <p class="text-sm text-gray-600 mb-4">Deja estos campos en blanco si no deseas cambiar tu contraseña</p>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                            Contraseña Actual
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                               id="password"
                               name="password"
                               type="password">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="new_password">
                            Nueva Contraseña
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                               id="new_password"
                               name="new_password"
                               type="password">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button class="bg-chocolate-darker hover:bg-chocolate-dark text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="submit">
                        Guardar Cambios
                    </button>
                    <a href="index.php"
                       class="text-chocolate-darker hover:text-chocolate-dark font-bold">
                        Volver
                    </a>
                </div>
            </form>
        </div>
    </main>
</body>
</html> 