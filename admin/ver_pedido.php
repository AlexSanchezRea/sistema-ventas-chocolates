<?php
session_start();
require_once '../config/database.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Acceso denegado']);
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'ID de pedido inválido']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener información del pedido y cliente
$stmt = $db->prepare("
    SELECT 
        p.*,
        u.nombre as nombre_cliente,
        u.email as email_cliente
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    WHERE p.id = ?
");
$stmt->execute([$_GET['id']]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => 'Pedido no encontrado']);
    exit();
}

// Obtener detalles de los productos del pedido
$stmt = $db->prepare("
    SELECT 
        dp.*,
        p.nombre as nombre_producto
    FROM detalles_pedido dp
    JOIN productos p ON dp.producto_id = p.id
    WHERE dp.pedido_id = ?
");
$stmt->execute([$_GET['id']]);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preparar la respuesta
$respuesta = [
    'id' => $pedido['id'],
    'cliente' => [
        'nombre' => $pedido['nombre_cliente'],
        'email' => $pedido['email_cliente']
    ],
    'fecha' => $pedido['fecha_pedido'],
    'estado' => $pedido['estado'],
    'total' => $pedido['total'],
    'productos' => array_map(function($producto) {
        return [
            'nombre' => $producto['nombre_producto'],
            'cantidad' => $producto['cantidad'],
            'precio_unitario' => $producto['precio_unitario'],
            'subtotal' => $producto['subtotal']
        ];
    }, $productos)
];

header('Content-Type: application/json');
echo json_encode($respuesta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Pedido</title>
    <link rel="stylesheet" href="../css/tailwind.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="bg-gray-100">
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-lg p-8 mt-10">
        <h2 class="text-2xl font-bold mb-6 text-chocolate">Detalles del Pedido #<?php echo $pedido['id']; ?></h2>
        <div class="mb-4">
            <h3 class="font-semibold text-lg text-chocolate">Cliente</h3>
            <p><span class="font-medium">Nombre:</span> <?php echo htmlspecialchars($pedido['nombre_cliente']); ?></p>
            <p><span class="font-medium">Email:</span> <?php echo htmlspecialchars($pedido['email_cliente']); ?></p>
        </div>
        <div class="mb-4">
            <h3 class="font-semibold text-lg text-chocolate">Datos del Pedido</h3>
            <p><span class="font-medium">Fecha:</span> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
            <p><span class="font-medium">Estado:</span> <?php echo ucfirst($pedido['estado']); ?></p>
            <p><span class="font-medium">Total:</span> Bs. <?php echo number_format($pedido['total'], 2); ?></p>
        </div>
        <div>
            <h3 class="font-semibold text-lg text-chocolate mb-2">Productos</h3>
            <table class="min-w-full bg-white border rounded">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Producto</th>
                        <th class="py-2 px-4 border-b">Cantidad</th>
                        <th class="py-2 px-4 border-b">Precio Unitario</th>
                        <th class="py-2 px-4 border-b">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod): ?>
                    <tr>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($prod['nombre']); ?></td>
                        <td class="py-2 px-4 border-b text-center"><?php echo $prod['cantidad']; ?></td>
                        <td class="py-2 px-4 border-b text-right">Bs. <?php echo number_format($prod['precio_unitario'], 2); ?></td>
                        <td class="py-2 px-4 border-b text-right">Bs. <?php echo number_format($prod['subtotal'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-6 text-center">
            <a href="pedidos.php" class="admin-button">Volver</a>
        </div>
    </div>
</body>
</html> 