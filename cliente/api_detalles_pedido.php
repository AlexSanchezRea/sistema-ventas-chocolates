<?php
require_once '../config/database.php';
require_once '../includes/session.php';

// Verificar si el usuario está logueado como cliente
if (!isClient()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Verificar si se proporcionó el ID del pedido
if (!isset($_GET['pedido_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID de pedido no proporcionado']);
    exit();
}

$pedidoId = intval($_GET['pedido_id']);
$userId = getCurrentUserId();

try {
    $conn = getConnection();
    
    // Primero verificar que el pedido pertenece al usuario
    $stmt = $conn->prepare("SELECT id FROM pedidos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$pedidoId, $userId]);
    
    if (!$stmt->fetch()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
        exit();
    }
    
    // Obtener los detalles del pedido
    $stmt = $conn->prepare("
        SELECT 
            dp.*,
            p.nombre,
            p.imagen
        FROM detalles_pedido dp
        JOIN productos p ON dp.producto_id = p.id
        WHERE dp.pedido_id = ?
        ORDER BY dp.id
    ");
    $stmt->execute([$pedidoId]);
    $detalles = $stmt->fetchAll();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'detalles' => $detalles]);
    
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error al obtener los detalles del pedido']);
}
?> 