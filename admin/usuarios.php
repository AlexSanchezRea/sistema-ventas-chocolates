<?php
session_start();
require_once '../config/database.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Cambiar rol de usuario
if (isset($_POST['usuario_id']) && isset($_POST['nuevo_rol'])) {
    $stmt = $db->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
    $stmt->execute([$_POST['nuevo_rol'], $_POST['usuario_id']]);
    header('Location: usuarios.php');
    exit();
}

// Eliminar usuario
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    // No permitir eliminar al usuario actual
    if ($_GET['delete'] != $_SESSION['usuario']) {
        $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
    }
    header('Location: usuarios.php');
    exit();
}

// Obtener todos los usuarios excepto el administrador actual
$stmt = $db->prepare("
    SELECT * FROM usuarios 
    WHERE id != ? 
    ORDER BY fecha_registro DESC
");
$stmt->execute([$_SESSION['usuario']]);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sweet Mett</title>
    <link rel="stylesheet" href="../css/tailwind.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="admin-body min-h-screen">
    <?php
    require_once '../config/database.php';
    require_once '../includes/session.php';

    if (!isAdmin()) {
        header('Location: ../login.php');
        exit();
    }

    $conn = getConnection();
    
    // Obtener todos los usuarios excepto el admin
    $stmt = $conn->query("SELECT * FROM usuarios WHERE rol != 'admin' ORDER BY nombre");
    $usuarios = $stmt->fetchAll();
    ?>

    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 chocolate-pattern">
            <div class="p-6">
                <img src="../assets/logo-blanco.png" alt="Sweet Mett" class="h-12 mx-auto">
            </div>
            <nav class="mt-6">
                <a href="dashboard.php" class="admin-nav-link">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Dashboard
                </a>
                <a href="pedidos.php" class="admin-nav-link">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                    </svg>
                    Pedidos
                </a>
                <a href="productos.php" class="admin-nav-link">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    Productos
                </a>
                <a href="usuarios.php" class="admin-nav-link active">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                    Usuarios
                </a>
                <a href="../logout.php" class="admin-nav-link mt-6">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                    </svg>
                    Cerrar Sesión
                </a>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="flex-1 overflow-y-auto">
            <div class="p-8">
                <h1 class="text-3xl font-semibold text-chocolate mb-8">Gestión de Usuarios</h1>

                <!-- Lista de usuarios -->
                <div class="glass-effect rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Dirección</th>
                                    <th>Fecha de Registro</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td>#<?php echo $usuario['id']; ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['direccion']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $usuario['activo'] ? 'completed' : 'cancelled'; ?>">
                                            <?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </td>
                                    <td class="space-x-2">
                                        <button onclick="cambiarEstado(<?php echo $usuario['id']; ?>, <?php echo $usuario['activo']; ?>)"
                                                class="admin-button secondary text-sm">
                                            <?php echo $usuario['activo'] ? 'Desactivar' : 'Activar'; ?>
                                        </button>
                                        <button onclick="eliminarUsuario(<?php echo $usuario['id']; ?>)"
                                                class="admin-button text-sm bg-red-600 hover:bg-red-700">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    function cambiarEstado(idUsuario, estadoActual) {
        const nuevoEstado = !estadoActual;
        if (confirm(`¿Está seguro de que desea ${nuevoEstado ? 'activar' : 'desactivar'} este usuario?`)) {
            // Aquí deberías hacer una llamada AJAX para cambiar el estado
            // Por ahora, simplemente recargamos la página
            window.location.reload();
        }
    }

    function eliminarUsuario(id) {
        if (confirm('¿Está seguro de que desea eliminar este usuario? Esta acción no se puede deshacer.')) {
            // Aquí deberías hacer una llamada AJAX para eliminar el usuario
            // Por ahora, simplemente recargamos la página
            window.location.reload();
        }
    }
    </script>
</body>
</html> 