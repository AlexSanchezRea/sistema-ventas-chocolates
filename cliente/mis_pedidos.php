<?php
require_once '../config/database.php';
require_once '../includes/session.php';

// Verificar si el usuario está logueado como cliente
if (!isClient()) {
    header('Location: ../login.php');
    exit();
}

// Obtener los pedidos del usuario
$conn = getConnection();
$userId = getCurrentUserId();
$stmt = $conn->prepare("
    SELECT p.*, COUNT(dp.id) as total_items
    FROM pedidos p
    LEFT JOIN detalles_pedido dp ON p.id = dp.pedido_id
    WHERE p.usuario_id = ?
    GROUP BY p.id
    ORDER BY p.fecha_pedido DESC
");
$stmt->execute([$userId]);
$pedidos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - Sweet Mett</title>
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
        <h1 class="text-3xl font-bold text-chocolate-darker mb-8">Mis Pedidos</h1>

        <?php if (empty($pedidos)): ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-gray-500 text-center">Aún no has realizado ningún pedido</p>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($pedidos as $pedido): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-xl font-semibold text-chocolate-darker">
                                        Pedido #<?php echo $pedido['id']; ?>
                                    </h2>
                                    <p class="text-gray-600 mt-1">
                                        Fecha: <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?>
                                    </p>
                                    <p class="text-gray-600">
                                        Total de productos: <?php echo $pedido['total_items']; ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                        <?php
                                        switch ($pedido['estado']) {
                                            case 'pendiente':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'procesando':
                                                echo 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'completado':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            case 'cancelado':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                        }
                                        ?>">
                                        <?php echo ucfirst($pedido['estado']); ?>
                                    </span>
                                    <p class="mt-2 text-xl font-bold text-chocolate-darker">
                                        Bs. <?php echo number_format($pedido['total'], 2); ?>
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Botón para ver detalles -->
                            <button onclick="verDetalles(<?php echo $pedido['id']; ?>)" 
                                    class="mt-4 text-chocolate-darker hover:text-chocolate-dark flex items-center">
                                <span>Ver detalles</span>
                                <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Contenedor de detalles (inicialmente oculto) -->
                            <div id="detalles-<?php echo $pedido['id']; ?>" class="mt-4 hidden">
                                <div class="border-t border-gray-200 pt-4">
                                    <h3 class="text-lg font-semibold text-chocolate-darker mb-4">Productos del pedido</h3>
                                    <div class="space-y-4" id="productos-<?php echo $pedido['id']; ?>">
                                        <!-- Los productos se cargarán aquí mediante AJAX -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script>
    function verDetalles(pedidoId) {
        const detallesDiv = document.getElementById(`detalles-${pedidoId}`);
        const productosDiv = document.getElementById(`productos-${pedidoId}`);

        // Toggle la visibilidad
        if (detallesDiv.classList.contains('hidden')) {
            detallesDiv.classList.remove('hidden');
            
            // Cargar los detalles si no se han cargado antes
            if (productosDiv.children.length === 0) {
                fetch(`api_detalles_pedido.php?pedido_id=${pedidoId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let html = '';
                            data.detalles.forEach(detalle => {
                                html += `
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h4 class="font-medium text-gray-900">${detalle.nombre}</h4>
                                            <p class="text-sm text-gray-500">
                                                Cantidad: ${detalle.cantidad} x Bs. ${parseFloat(detalle.precio_unitario).toFixed(2)}
                                            </p>
                                        </div>
                                        <p class="font-medium text-gray-900">
                                            Bs. ${parseFloat(detalle.subtotal).toFixed(2)}
                                        </p>
                                    </div>
                                `;
                            });
                            productosDiv.innerHTML = html;
                        } else {
                            productosDiv.innerHTML = '<p class="text-red-500">Error al cargar los detalles</p>';
                        }
                    });
            }
        } else {
            detallesDiv.classList.add('hidden');
        }
    }
    </script>
</body>
</html> 