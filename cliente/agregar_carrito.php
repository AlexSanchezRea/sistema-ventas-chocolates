<?php
require_once '../config/database.php';
require_once '../includes/session.php';

// Verificar que sea cliente
if (!isClient()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit();
}

// Verificar que se recibieron los datos
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['producto_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

$producto_id = $data['producto_id'];
$usuario_id = $_SESSION['usuario'];

$database = new Database();
$db = $database->getConnection();

try {
    // Iniciar transacción
    $db->beginTransaction();

    // Verificar si el producto existe y tiene stock
    $stmt = $db->prepare("SELECT id, stock, precio FROM productos WHERE id = ? AND stock > 0");
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        throw new Exception('Producto no disponible');
    }

    // Verificar si ya existe un pedido pendiente
    $stmt = $db->prepare("SELECT id FROM pedidos WHERE usuario_id = ? AND estado = 'pendiente'");
    $stmt->execute([$usuario_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        // Crear nuevo pedido
        $stmt = $db->prepare("INSERT INTO pedidos (usuario_id, fecha_pedido, estado, total) VALUES (?, NOW(), 'pendiente', 0)");
        $stmt->execute([$usuario_id]);
        $pedido_id = $db->lastInsertId();
    } else {
        $pedido_id = $pedido['id'];
    }

    // Verificar si el producto ya está en el carrito
    $stmt = $db->prepare("
        SELECT id, cantidad 
        FROM detalles_pedido 
        WHERE pedido_id = ? AND producto_id = ?
    ");
    $stmt->execute([$pedido_id, $producto_id]);
    $detalle = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($detalle) {
        // Actualizar cantidad
        $nueva_cantidad = $detalle['cantidad'] + 1;
        if ($nueva_cantidad > $producto['stock']) {
            throw new Exception('Stock insuficiente');
        }

        $stmt = $db->prepare("
            UPDATE detalles_pedido 
            SET cantidad = ?, subtotal = cantidad * ?
            WHERE id = ?
        ");
        $stmt->execute([$nueva_cantidad, $producto['precio'], $detalle['id']]);
    } else {
        // Agregar nuevo detalle
        $stmt = $db->prepare("
            INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario, subtotal)
            VALUES (?, ?, 1, ?, ?)
        ");
        $stmt->execute([$pedido_id, $producto_id, $producto['precio'], $producto['precio']]);
    }

    // Actualizar total del pedido
    $stmt = $db->prepare("
        UPDATE pedidos p
        SET total = (
            SELECT COALESCE(SUM(subtotal), 0)
            FROM detalles_pedido
            WHERE pedido_id = p.id
        )
        WHERE id = ?
    ");
    $stmt->execute([$pedido_id]);

    // Confirmar transacción
    $db->commit();

    // Obtener nuevo total de items en el carrito
    $stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM detalles_pedido dp 
        JOIN pedidos p ON dp.pedido_id = p.id 
        WHERE p.usuario_id = ? AND p.estado = 'pendiente'
    ");
    $stmt->execute([$usuario_id]);
    $items_carrito = $stmt->fetchColumn();

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Producto agregado al carrito',
        'items_carrito' => $items_carrito
    ]);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    $db->rollBack();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 