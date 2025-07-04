<?php
require_once '../config/database.php';
require_once '../includes/session.php';

// Verificar que sea administrador
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Obtener conexión a la base de datos
$conn = getConnection();

// Obtener estadísticas
$stats = [
    'total_productos' => 0,
    'total_pedidos' => 0,
    'total_usuarios' => 0,
    'ingresos_totales' => 0,
    'pedidos_pendientes' => 0,
    'ingresos_hoy' => 0,
    'pedidos_hoy' => 0,
    'productos_sin_stock' => 0
];

// Total productos
$stmt = $conn->query("SELECT COUNT(*) FROM productos");
$stats['total_productos'] = $stmt->fetchColumn();

// Total pedidos
$stmt = $conn->query("SELECT COUNT(*) FROM pedidos");
$stats['total_pedidos'] = $stmt->fetchColumn();

// Total usuarios
$stmt = $conn->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'cliente'");
$stats['total_usuarios'] = $stmt->fetchColumn();

// Ingresos totales
$stmt = $conn->query("SELECT SUM(total) FROM pedidos WHERE estado = 'completado'");
$stats['ingresos_totales'] = $stmt->fetchColumn() ?: 0;

// Pedidos pendientes
$stmt = $conn->query("SELECT COUNT(*) FROM pedidos WHERE estado = 'pendiente'");
$stats['pedidos_pendientes'] = $stmt->fetchColumn();

// Ingresos de hoy
$stmt = $conn->query("SELECT SUM(total) FROM pedidos WHERE estado = 'completado' AND DATE(fecha_pedido) = CURDATE()");
$stats['ingresos_hoy'] = $stmt->fetchColumn() ?: 0;

// Pedidos de hoy
$stmt = $conn->query("SELECT COUNT(*) FROM pedidos WHERE DATE(fecha_pedido) = CURDATE()");
$stats['pedidos_hoy'] = $stmt->fetchColumn();

// Productos sin stock
$stmt = $conn->query("SELECT COUNT(*) FROM productos WHERE stock = 0");
$stats['productos_sin_stock'] = $stmt->fetchColumn();

// Últimos pedidos
$stmt = $conn->query("
    SELECT p.*, u.nombre as cliente
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    ORDER BY p.fecha_pedido DESC
    LIMIT 5
");
$ultimos_pedidos = $stmt->fetchAll();

// Productos con poco stock
$stmt = $conn->query("
    SELECT p.*, c.nombre as categoria_nombre
    FROM productos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    WHERE p.stock < 10
    ORDER BY p.stock ASC
    LIMIT 5
");
$productos_poco_stock = $stmt->fetchAll();

// Ventas por categoría
$stmt = $conn->query("
    SELECT 
        c.nombre as categoria,
        COUNT(dp.id) as total_ventas,
        SUM(dp.cantidad) as unidades_vendidas,
        SUM(dp.subtotal) as ingresos
    FROM categorias c
    LEFT JOIN productos p ON c.id = p.categoria_id
    LEFT JOIN detalles_pedido dp ON p.id = dp.producto_id
    LEFT JOIN pedidos ped ON dp.pedido_id = ped.id AND ped.estado = 'completado'
    GROUP BY c.id
    ORDER BY ingresos DESC
");
$ventas_por_categoria = $stmt->fetchAll();

// Productos más vendidos
$stmt = $conn->query("
    SELECT 
        p.nombre,
        p.precio,
        p.stock,
        COUNT(dp.id) as total_ventas,
        SUM(dp.cantidad) as unidades_vendidas
    FROM productos p
    LEFT JOIN detalles_pedido dp ON p.id = dp.producto_id
    LEFT JOIN pedidos ped ON dp.pedido_id = ped.id AND ped.estado = 'completado'
    GROUP BY p.id
    ORDER BY unidades_vendidas DESC
    LIMIT 5
");
$productos_mas_vendidos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Sweet Mett</title>
    <link rel="stylesheet" href="../css/tailwind.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-chocolate-darker text-white">
            <div class="p-6">
                <img src="../assets/logo-blanco.png" alt="Sweet Mett" class="h-12 mx-auto">
            </div>
            <nav class="mt-6">
                <a href="dashboard.php" class="flex items-center px-6 py-3 text-white bg-chocolate-dark">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Dashboard
                </a>
                <a href="pedidos.php" class="flex items-center px-6 py-3 text-white hover:bg-chocolate-dark">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                    </svg>
                    Pedidos
                </a>
                <a href="productos.php" class="flex items-center px-6 py-3 text-white hover:bg-chocolate-dark">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    Productos
                </a>
                <a href="usuarios.php" class="flex items-center px-6 py-3 text-white hover:bg-chocolate-dark">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                    Usuarios
                </a>
                <a href="../logout.php" class="flex items-center px-6 py-3 mt-6 text-white hover:bg-chocolate-dark">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                    </svg>
                    Cerrar Sesión
                </a>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="flex-1 overflow-y-auto p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Tarjeta de Pedidos -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-amber-100 text-amber-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600">Total Pedidos</h2>
                            <p class="text-2xl font-semibold text-gray-700"><?php echo $stats['total_pedidos']; ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center">
                            <span class="text-green-500 text-sm font-semibold">
                                <?php echo $stats['pedidos_hoy']; ?> hoy
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Ingresos -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600">Ingresos Totales</h2>
                            <p class="text-2xl font-semibold text-gray-700">Bs. <?php echo number_format($stats['ingresos_totales'], 2); ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center">
                            <span class="text-green-500 text-sm font-semibold">
                                Bs. <?php echo number_format($stats['ingresos_hoy'], 2); ?> hoy
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Usuarios -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600">Total Usuarios</h2>
                            <p class="text-2xl font-semibold text-gray-700"><?php echo $stats['total_usuarios']; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Productos -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600">Total Productos</h2>
                            <p class="text-2xl font-semibold text-gray-700"><?php echo $stats['total_productos']; ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center">
                            <span class="text-red-500 text-sm font-semibold">
                                <?php echo $stats['productos_sin_stock']; ?> sin stock
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Últimos Pedidos -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Últimos Pedidos</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left">ID</th>
                                        <th class="px-4 py-2 text-left">Cliente</th>
                                        <th class="px-4 py-2 text-left">Total</th>
                                        <th class="px-4 py-2 text-left">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ultimos_pedidos as $pedido): ?>
                                        <tr>
                                            <td class="px-4 py-2">#<?php echo $pedido['id']; ?></td>
                                            <td class="px-4 py-2"><?php echo htmlspecialchars($pedido['cliente']); ?></td>
                                            <td class="px-4 py-2">Bs. <?php echo number_format($pedido['total'], 2); ?></td>
                                            <td class="px-4 py-2">
                                                <span class="px-2 py-1 text-xs rounded-full <?php echo $pedido['estado'] == 'completado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                    <?php echo ucfirst($pedido['estado']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Productos con Poco Stock -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Productos con Poco Stock</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left">Producto</th>
                                        <th class="px-4 py-2 text-left">Categoría</th>
                                        <th class="px-4 py-2 text-left">Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productos_poco_stock as $producto): ?>
                                        <tr>
                                            <td class="px-4 py-2"><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                            <td class="px-4 py-2"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></td>
                                            <td class="px-4 py-2">
                                                <span class="px-2 py-1 text-xs rounded-full <?php echo $producto['stock'] == 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                    <?php echo $producto['stock']; ?> unidades
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 