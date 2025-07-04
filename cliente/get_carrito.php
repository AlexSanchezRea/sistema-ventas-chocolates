<?php
require_once '../config/database.php';
require_once '../includes/session.php';

// Verificar que sea cliente
if (!isClient()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit();
}

$usuario_id = $_SESSION['usuario'];

$database = new Database();
$db = $database->getConnection();

try {
    // Obtener el pedido pendiente con sus detalles
    $stmt = $db->prepare("
        SELECT 
            dp.id,
            dp.cantidad,
            dp.precio_unitario,
            dp.subtotal,
            p.nombre,
            p.imagen,
            p.stock
        FROM pedidos ped
        JOIN detalles_pedido dp ON ped.id = dp.pedido_id
        JOIN productos p ON dp.producto_id = p.id
        WHERE ped.usuario_id = ? AND ped.estado = 'pendiente'
    ");
    $stmt->execute([$usuario_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener el total del pedido
    $stmt = $db->prepare("
        SELECT total 
        FROM pedidos 
        WHERE usuario_id = ? AND estado = 'pendiente'
    ");
    $stmt->execute([$usuario_id]);
    $total = $stmt->fetchColumn() ?: 0;

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => (float)$total
    ]);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 