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

// Actualizar estado del pedido
if (isset($_POST['pedido_id']) && isset($_POST['nuevo_estado'])) {
    $stmt = $db->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
    $stmt->execute([$_POST['nuevo_estado'], $_POST['pedido_id']]);
    header('Location: pedidos.php');
    exit();
}

// Obtener todos los pedidos con información del usuario
$stmt = $db->query("
    SELECT 
        p.*,
        u.nombre as nombre_usuario,
        u.email as email_usuario
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    ORDER BY p.fecha_pedido DESC
");
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos - Sweet Mett</title>
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
    
    // Obtener todos los pedidos con información del cliente
    $stmt = $conn->query("SELECT p.*, u.nombre as nombre_cliente 
                         FROM pedidos p 
                         JOIN usuarios u ON p.usuario_id = u.id 
                         ORDER BY p.fecha_pedido DESC");
    $pedidos = $stmt->fetchAll();
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
                <a href="pedidos.php" class="admin-nav-link active">
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
                <h1 class="text-3xl font-semibold text-chocolate mb-8">Gestión de Pedidos</h1>

                <!-- Lista de pedidos -->
                <div class="glass-effect rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td>#<?php echo $pedido['id']; ?></td>
                                    <td><?php echo htmlspecialchars($pedido['nombre_cliente']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $pedido['estado']; ?>">
                                            <?php echo ucfirst($pedido['estado']); ?>
                                        </span>
                                    </td>
                                    <td>Bs. <?php echo number_format($pedido['total'], 2); ?></td>
                                    <td class="space-x-2">
                                        <button onclick="verDetallesPedido(<?php echo $pedido['id']; ?>)" class="admin-button">Ver detalles</button>
                                        <button onclick="actualizarEstado(<?php echo $pedido['id']; ?>)"
                                                class="admin-button text-sm">
                                            Actualizar Estado
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

    <!-- Modal para detalles del pedido -->
    <div id="modal-detalles" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
      <div class="bg-white rounded-xl shadow-lg max-w-lg w-full p-8 relative">
        <button onclick="cerrarModalDetalles()" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl">&times;</button>
        <div id="contenido-detalles"></div>
      </div>
    </div>

    <script>
    function actualizarEstado(idPedido) {
        const nuevoEstado = prompt('Ingrese el nuevo estado (pendiente, procesando, completado, cancelado):');
        if (nuevoEstado) {
            const estados = ['pendiente', 'procesando', 'completado', 'cancelado'];
            if (estados.includes(nuevoEstado.toLowerCase())) {
                // Aquí deberías hacer una llamada AJAX para actualizar el estado
                // Por ahora, simplemente recargamos la página
                window.location.reload();
            } else {
                alert('Estado no válido. Los estados permitidos son: pendiente, procesando, completado, cancelado');
            }
        }
    }

    function verDetallesPedido(id) {
      fetch('ver_pedido.php?id=' + id)
        .then(res => res.json())
        .then(data => {
          if (data.error) {
            document.getElementById('contenido-detalles').innerHTML = '<div class="text-red-600">'+data.error+'</div>';
          } else {
            document.getElementById('contenido-detalles').innerHTML = `
              <h2 class="text-2xl font-bold mb-4 text-chocolate">Pedido #${data.id}</h2>
              <div class="mb-4">
                <h3 class="font-semibold text-lg text-chocolate">Cliente</h3>
                <p><span class="font-medium">Nombre:</span> ${data.cliente.nombre}</p>
                <p><span class="font-medium">Email:</span> ${data.cliente.email}</p>
              </div>
              <div class="mb-4">
                <h3 class="font-semibold text-lg text-chocolate">Datos del Pedido</h3>
                <p><span class="font-medium">Fecha:</span> ${new Date(data.fecha).toLocaleString()}</p>
                <p><span class="font-medium">Estado:</span> ${data.estado.charAt(0).toUpperCase() + data.estado.slice(1)}</p>
                <p><span class="font-medium">Total:</span> Bs. ${parseFloat(data.total).toFixed(2)}</p>
              </div>
              <div>
                <h3 class="font-semibold text-lg text-chocolate mb-2">Productos</h3>
                <table class="min-w-full bg-white border rounded mb-2">
                  <thead>
                    <tr>
                      <th class="py-2 px-4 border-b">Producto</th>
                      <th class="py-2 px-4 border-b">Cantidad</th>
                      <th class="py-2 px-4 border-b">Precio Unitario</th>
                      <th class="py-2 px-4 border-b">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${data.productos.map(prod => `
                      <tr>
                        <td class="py-2 px-4 border-b">${prod.nombre}</td>
                        <td class="py-2 px-4 border-b text-center">${prod.cantidad}</td>
                        <td class="py-2 px-4 border-b text-right">Bs. ${parseFloat(prod.precio_unitario).toFixed(2)}</td>
                        <td class="py-2 px-4 border-b text-right">Bs. ${parseFloat(prod.subtotal).toFixed(2)}</td>
                      </tr>
                    `).join('')}
                  </tbody>
                </table>
              </div>
            `;
          }
          document.getElementById('modal-detalles').classList.remove('hidden');
        });
    }

    function cerrarModalDetalles() {
      document.getElementById('modal-detalles').classList.add('hidden');
    }
    </script>
</body>
</html>