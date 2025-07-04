<?php
require_once '../config/database.php';
require_once '../includes/session.php';

// Verificar que sea cliente
if (!isClient()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit();
}

// Obtener datos del formulario
$data = json_decode(file_get_contents('php://input'), true);
$direccion = $data['direccion'] ?? '';
$telefono = $data['telefono'] ?? '';
$notas = $data['notas'] ?? '';

if (empty($direccion) || empty($telefono)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Por favor complete los datos de entrega']);
    exit();
}

$usuario_id = $_SESSION['usuario'];

$database = new Database();
$db = $database->getConnection();

try {
    // Iniciar transacción
    $db->beginTransaction();

    // Obtener el pedido pendiente
    $stmt = $db->prepare("
        SELECT p.*, dp.producto_id, dp.cantidad
        FROM pedidos p
        JOIN detalles_pedido dp ON p.id = dp.pedido_id
        WHERE p.usuario_id = ? AND p.estado = 'pendiente'
    ");
    $stmt->execute([$usuario_id]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($detalles)) {
        throw new Exception('No hay productos en el carrito');
    }

    $pedido_id = $detalles[0]['id'];

    // Verificar stock disponible para cada producto
    foreach ($detalles as $detalle) {
        $stmt = $db->prepare("SELECT stock FROM productos WHERE id = ? FOR UPDATE");
        $stmt->execute([$detalle['producto_id']]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$producto || $producto['stock'] < $detalle['cantidad']) {
            throw new Exception('Stock insuficiente para algunos productos');
        }

        // Actualizar stock
        $stmt = $db->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$detalle['cantidad'], $detalle['producto_id']]);
    }

    // Actualizar datos del pedido
    $stmt = $db->prepare("
        UPDATE pedidos 
        SET estado = 'procesando',
            fecha_actualizacion = NOW(),
            direccion_entrega = ?,
            telefono_contacto = ?,
            notas = ?
        WHERE id = ?
    ");
    $stmt->execute([$direccion, $telefono, $notas, $pedido_id]);

    // Confirmar transacción
    $db->commit();

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => '¡Pedido realizado con éxito!'
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