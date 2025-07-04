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

// Obtener todas las categorías
$stmt = $db->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar formulario de agregar/editar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $stmt = $db->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['nombre'],
                $_POST['descripcion'],
                $_POST['precio'],
                $_POST['stock'],
                $_POST['categoria_id']
            ]);

            // Procesar imagen si se subió una
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                $imagen = $_FILES['imagen'];
                $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
                $nuevo_nombre = uniqid() . '.' . $extension;
                $ruta_destino = '../assets/productos/' . $nuevo_nombre;
                
                if (move_uploaded_file($imagen['tmp_name'], $ruta_destino)) {
                    $stmt = $db->prepare("UPDATE productos SET imagen = ? WHERE id = ?");
                    $stmt->execute([$nuevo_nombre, $db->lastInsertId()]);
                }
            }
        } elseif ($_POST['action'] === 'update' && isset($_POST['id'])) {
            $stmt = $db->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, categoria_id = ? WHERE id = ?");
            $stmt->execute([
                $_POST['nombre'],
                $_POST['descripcion'],
                $_POST['precio'],
                $_POST['stock'],
                $_POST['categoria_id'],
                $_POST['id']
            ]);

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                $imagen = $_FILES['imagen'];
                $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
                $nuevo_nombre = uniqid() . '.' . $extension;
                $ruta_destino = '../assets/productos/' . $nuevo_nombre;
                
                if (move_uploaded_file($imagen['tmp_name'], $ruta_destino)) {
                    $stmt = $db->prepare("UPDATE productos SET imagen = ? WHERE id = ?");
                    $stmt->execute([$nuevo_nombre, $_POST['id']]);
                }
            }
        }
    }
    header('Location: productos.php');
    exit();
}

// Eliminar producto
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: productos.php');
    exit();
}

// Obtener todos los productos con sus categorías
$stmt = $db->query("
    SELECT 
        p.*,
        c.nombre as nombre_categoria
    FROM productos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    ORDER BY p.fecha_creacion DESC
");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Sweet Mett</title>
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
    
    // Obtener todos los productos
    $stmt = $conn->query("SELECT * FROM productos ORDER BY nombre");
    $productos = $stmt->fetchAll();
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
                <a href="productos.php" class="admin-nav-link active">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    Productos
                </a>
                <a href="usuarios.php" class="admin-nav-link">
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
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-semibold text-chocolate">Gestión de Productos</h1>
                    <button onclick="mostrarFormulario()" class="admin-button">
                        Agregar Producto
                    </button>
                </div>

                <!-- Lista de productos -->
                <div class="glass-effect rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td>#<?php echo $producto['id']; ?></td>
                                    <td>
                                        <img src="<?php echo $producto['imagen']; ?>" 
                                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                             class="w-16 h-16 object-cover rounded-lg">
                                    </td>
                                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['descripcion']); ?></td>
                                    <td>Bs. <?php echo number_format($producto['precio'], 2); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $producto['stock'] < 10 ? 'cancelled' : 'completed'; ?>">
                                            <?php echo $producto['stock']; ?> unidades
                                        </span>
                                    </td>
                                    <td class="space-x-2">
                                        <button onclick="editarProducto(<?php echo htmlspecialchars(json_encode($producto)); ?>)"
                                                class="admin-button secondary text-sm">
                                            Editar
                                        </button>
                                        <button onclick="eliminarProducto(<?php echo $producto['id']; ?>)"
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

    <!-- Modal para agregar/editar producto -->
    <div id="modalProducto" class="fixed inset-0 bg-black bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="admin-modal w-full max-w-2xl p-8">
                <h2 id="modalTitulo" class="text-2xl font-semibold text-chocolate mb-6">Agregar Producto</h2>
                <form id="formProducto" class="space-y-6">
                    <input type="hidden" id="productoId" name="id">
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="admin-input" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Precio</label>
                            <input type="number" id="precio" name="precio" step="0.01" class="admin-input" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3" class="admin-input"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                            <input type="number" id="stock" name="stock" class="admin-input" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Imagen</label>
                            <input type="file" id="imagen" name="imagen" class="admin-input" accept="image/*">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="cerrarModal()" 
                                class="admin-button secondary">
                            Cancelar
                        </button>
                        <button type="submit" class="admin-button">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function mostrarFormulario() {
        document.getElementById('modalTitulo').textContent = 'Agregar Producto';
        document.getElementById('formProducto').reset();
        document.getElementById('productoId').value = '';
        document.getElementById('modalProducto').classList.remove('hidden');
    }

    function editarProducto(producto) {
        document.getElementById('modalTitulo').textContent = 'Editar Producto';
        document.getElementById('productoId').value = producto.id;
        document.getElementById('nombre').value = producto.nombre;
        document.getElementById('precio').value = producto.precio;
        document.getElementById('descripcion').value = producto.descripcion;
        document.getElementById('stock').value = producto.stock;
        document.getElementById('modalProducto').classList.remove('hidden');
    }

    function cerrarModal() {
        document.getElementById('modalProducto').classList.add('hidden');
    }

    function eliminarProducto(id) {
        if (confirm('¿Está seguro de que desea eliminar este producto?')) {
            // Aquí deberías hacer una llamada AJAX para eliminar el producto
            // Por ahora, simplemente recargamos la página
            window.location.reload();
        }
    }

    document.getElementById('formProducto').addEventListener('submit', function(e) {
        e.preventDefault();
        // Aquí deberías hacer una llamada AJAX para guardar el producto
        // Por ahora, simplemente recargamos la página
        window.location.reload();
    });
    </script>
</body>
</html> 