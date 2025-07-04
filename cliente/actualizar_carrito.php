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

if (!isset($data['id']) || !isset($data['accion'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

$detalle_id = $data['id'];
$accion = $data['accion'];
$usuario_id = $_SESSION['usuario'];

$database = new Database();
$db = $database->getConnection();

try {
    // Iniciar transacción
    $db->beginTransaction();

    // Verificar que el detalle pertenezca a un pedido del usuario
    $stmt = $db->prepare("
        SELECT dp.*, p.stock, p.precio
        FROM detalles_pedido dp
        JOIN pedidos ped ON dp.pedido_id = ped.id
        JOIN productos p ON dp.producto_id = p.id
        WHERE dp.id = ? AND ped.usuario_id = ? AND ped.estado = 'pendiente'
        FOR UPDATE
    ");
    $stmt->execute([$detalle_id, $usuario_id]);
    $detalle = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$detalle) {
        throw new Exception('Producto no encontrado en el carrito');
    }

    // Calcular nueva cantidad
    $nueva_cantidad = $detalle['cantidad'];
    if ($accion === 'sumar') {
        if ($nueva_cantidad >= $detalle['stock']) {
            throw new Exception('Stock insuficiente');
        }
        $nueva_cantidad++;
    } else if ($accion === 'restar') {
        $nueva_cantidad--;
    }

    // Si la cantidad es 0, eliminar el detalle
    if ($nueva_cantidad <= 0) {
        $stmt = $db->prepare("DELETE FROM detalles_pedido WHERE id = ?");
        $stmt->execute([$detalle_id]);
    } else {
        // Actualizar cantidad y subtotal
        $stmt = $db->prepare("
            UPDATE detalles_pedido 
            SET cantidad = ?, subtotal = cantidad * ?
            WHERE id = ?
        ");
        $stmt->execute([$nueva_cantidad, $detalle['precio'], $detalle_id]);
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
    $stmt->execute([$detalle['pedido_id']]);

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
        'message' => 'Carrito actualizado',
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