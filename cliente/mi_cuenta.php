<?php
require_once '../config/database.php';
require_once '../includes/session.php';

// Verificar que el usuario esté logueado y sea cliente
if (!isClient()) {
    header('Location: ../login.php');
    exit;
}

// Obtener cantidad de items en el carrito
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->prepare("SELECT COUNT(*) FROM carrito WHERE usuario_id = ?");
$stmt->execute([getCurrentUserId()]);
$itemsCarrito = $stmt->fetchColumn();

// Obtener información del usuario
$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([getCurrentUserId()]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener pedidos del usuario
$stmt = $db->prepare("
    SELECT p.*, COUNT(dp.id) as total_productos
    FROM pedidos p
    LEFT JOIN detalles_pedido dp ON p.id = dp.pedido_id
    WHERE p.usuario_id = ?
    GROUP BY p.id
    ORDER BY p.fecha_pedido DESC
");
$stmt->execute([getCurrentUserId()]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener productos en el carrito
$stmt = $db->prepare("
    SELECT c.*, p.nombre, p.precio, p.imagen
    FROM carrito c
    JOIN productos p ON c.producto_id = p.id
    WHERE c.usuario_id = ?
");
$stmt->execute([getCurrentUserId()]);
$carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta | Sweet Mett</title>
    <link rel="stylesheet" href="../css/tailwind.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Open+Sans:wght@400;700&family=Playfair+Display:wght@400;700&family=Pacifico&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        #main-nav {
            background-color: rgba(65, 45, 35, 0.7) !important;
            backdrop-filter: blur(5px);
        }
        #main-nav.scrolled {
            background-color: rgba(65, 45, 35, 0.85) !important;
            backdrop-filter: blur(8px);
        }
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #EF4444;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-amber-50">
    <!-- Navbar -->
    <nav id="main-nav" class="fixed w-full z-50 text-white shadow-lg transition-all duration-500 ease-in-out">
        <div class="container mx-auto px-6 py-2 md:py-3 flex justify-between items-center transition-all duration-500">
            <div class="flex items-center">
                <a href="../" class="flex items-center">
                    <img src="../assets/logo-blanco.png" width="70px" alt="logo de sweet mett" class="transition-all duration-500 logo-animate hover:opacity-80">
                </a>
            </div>

            <div class="hidden md:flex items-center space-x-6 font-medium">
                <a href="../index.php#nosotros" class="hover:text-gold transition duration-300">Nosotros</a>
                <a href="../index.php#productos" class="hover:text-gold transition duration-300">Productos</a>
                <a href="../index.php#galeria" class="hover:text-gold transition duration-300">Galería</a>
                <a href="../catalogo.php" class="hover:text-gold transition duration-300">Catálogo</a>
                <a href="mi_cuenta.php" class="hover:text-gold transition duration-300">Mi Cuenta</a>
                <a href="mi_cuenta.php" class="hover:text-gold transition duration-300 relative">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($itemsCarrito > 0): ?>
                        <span class="cart-count"><?php echo $itemsCarrito; ?></span>
                    <?php endif; ?>
                </a>
                <a href="../logout.php" class="ml-4 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-1.5 px-5 rounded-full transition duration-300">
                    Cerrar Sesión
                </a>
                <a href="https://wa.me/1234567890" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-1.5 px-5 rounded-full transition duration-300 flex items-center" target="_blank">
                    <i class="fab fa-whatsapp mr-2"></i>Contacto
                </a>
            </div>

            <!-- Menú móvil -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-white focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Menú móvil desplegable -->
        <div id="mobile-menu" class="hidden md:hidden bg-chocolate-darker">
            <div class="px-4 py-3 space-y-2">
                <a href="../index.php#nosotros" class="block py-2 hover:text-gold font-medium">Nosotros</a>
                <a href="../index.php#productos" class="block py-2 hover:text-gold font-medium">Productos</a>
                <a href="../index.php#galeria" class="block py-2 hover:text-gold font-medium">Galería</a>
                <a href="../catalogo.php" class="block py-2 hover:text-gold font-medium">Catálogo</a>
                <a href="mi_cuenta.php" class="block py-2 hover:text-gold font-medium">Mi Cuenta</a>
                <a href="mi_cuenta.php" class="block py-2 hover:text-gold font-medium">
                    Carrito
                    <?php if ($itemsCarrito > 0): ?>
                        <span class="ml-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs"><?php echo $itemsCarrito; ?></span>
                    <?php endif; ?>
                </a>
                <a href="../logout.php" class="block py-2 text-red-400 hover:text-red-300 font-medium">Cerrar Sesión</a>
                <a href="https://wa.me/1234567890" class="block py-2 text-green-400 hover:text-green-300 font-medium">
                    <i class="fab fa-whatsapp mr-2"></i>Contacto
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="container mx-auto px-6 py-24">
        <!-- Contenido Principal -->
        <div class="account-section">
            <h1 class="text-3xl font-bold mb-6">Mi Cuenta</h1>
            
            <!-- Pestañas -->
            <div class="account-tabs">
                <div class="account-tab active" data-tab="perfil">
                    <i class="fas fa-user"></i> Mi Perfil
                </div>
                <div class="account-tab" data-tab="pedidos">
                    <i class="fas fa-shopping-bag"></i> Mis Pedidos
                </div>
                <div class="account-tab" data-tab="carrito">
                    <i class="fas fa-shopping-cart"></i> Mi Carrito
                </div>
            </div>

            <!-- Contenido de las pestañas -->
            <div class="tab-content" id="perfil">
                <h2 class="text-2xl font-semibold mb-4">Información Personal</h2>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Nombre</label>
                            <p class="text-gray-900"><?php echo htmlspecialchars($usuario['nombre']); ?></p>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Email</label>
                            <p class="text-gray-900"><?php echo htmlspecialchars($usuario['email']); ?></p>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Teléfono</label>
                            <p class="text-gray-900"><?php echo htmlspecialchars($usuario['telefono'] ?? 'No especificado'); ?></p>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Dirección</label>
                            <p class="text-gray-900"><?php echo htmlspecialchars($usuario['direccion'] ?? 'No especificada'); ?></p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="mi_perfil.php" class="btn-primary">Editar Perfil</a>
                    </div>
                </div>
            </div>

            <div class="tab-content hidden" id="pedidos">
                <h2 class="text-2xl font-semibold mb-4">Historial de Pedidos</h2>
                <?php if (empty($pedidos)): ?>
                    <p class="text-gray-600">No tienes pedidos realizados.</p>
                <?php else: ?>
                    <div class="grid gap-4">
                        <?php foreach ($pedidos as $pedido): ?>
                            <div class="bg-white p-4 rounded-lg shadow-md">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="font-semibold">Pedido #<?php echo $pedido['id']; ?></h3>
                                        <p class="text-sm text-gray-600">
                                            Fecha: <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold">Total: Bs. <?php echo number_format($pedido['total'], 2); ?></p>
                                        <p class="text-sm text-gray-600">
                                            Estado: <span class="font-medium"><?php echo ucfirst($pedido['estado']); ?></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="api_detalles_pedido.php?pedido_id=<?php echo $pedido['id']; ?>" 
                                       class="text-chocolate-darker hover:text-chocolate-light">
                                        Ver detalles
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tab-content hidden" id="carrito">
                <h2 class="text-2xl font-semibold mb-4">Mi Carrito</h2>
                <?php if (empty($carrito)): ?>
                    <p class="text-gray-600">No tienes productos en el carrito.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg shadow-md">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">Producto</th>
                                    <th class="px-4 py-2">Precio</th>
                                    <th class="px-4 py-2">Cantidad</th>
                                    <th class="px-4 py-2">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total = 0; ?>
                                <?php foreach ($carrito as $item): ?>
                                    <tr>
                                        <td class="px-4 py-2 flex items-center gap-2">
                                            <img src="../assets/<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>" class="w-12 h-12 object-cover rounded">
                                            <span><?php echo htmlspecialchars($item['nombre']); ?></span>
                                        </td>
                                        <td class="px-4 py-2">Bs. <?php echo number_format($item['precio'], 2); ?></td>
                                        <td class="px-4 py-2"><?php echo $item['cantidad']; ?></td>
                                        <td class="px-4 py-2">Bs. <?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></td>
                                    </tr>
                                    <?php $total += $item['precio'] * $item['cantidad']; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="text-right mt-4">
                            <span class="font-bold text-lg">Total: Bs. <?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>
                    <div class="mt-6 text-right">
                        <button id="btn-finalizar-pedido" class="bg-gold hover:bg-yellow-500 text-chocolate-darker font-semibold py-2 px-6 rounded-full transition duration-300">Finalizar pedido</button>
                    </div>
                    <!-- Formulario de datos del pedido (oculto por defecto) -->
                    <form id="form-finalizar-pedido" action="finalizar_pedido.php" method="POST" class="bg-white p-6 rounded-lg shadow-md mt-6 max-w-lg mx-auto hidden">
                        <h3 class="text-xl font-semibold mb-4">Datos para el pedido</h3>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Nombre</label>
                            <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required class="w-full border border-gray-300 rounded px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Dirección</label>
                            <input type="text" name="direccion" value="<?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?>" required class="w-full border border-gray-300 rounded px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Teléfono</label>
                            <input type="text" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>" required class="w-full border border-gray-300 rounded px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Notas para el pedido (opcional)</label>
                            <textarea name="notas" class="w-full border border-gray-300 rounded px-3 py-2" rows="3"></textarea>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="bg-gold hover:bg-yellow-500 text-chocolate-darker font-semibold py-2 px-6 rounded-full transition duration-300">Guardar pedido</button>
                        </div>
                    </form>
                    <script>
                        document.getElementById('btn-finalizar-pedido').addEventListener('click', function() {
                            document.getElementById('form-finalizar-pedido').classList.toggle('hidden');
                            window.scrollTo({
                                top: document.getElementById('form-finalizar-pedido').offsetTop - 100,
                                behavior: 'smooth'
                            });
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        // Manejo de pestañas
        document.querySelectorAll('.account-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Remover clase active de todas las pestañas
                document.querySelectorAll('.account-tab').forEach(t => t.classList.remove('active'));
                // Agregar clase active a la pestaña clickeada
                tab.classList.add('active');
                
                // Ocultar todos los contenidos
                document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
                // Mostrar el contenido correspondiente
                document.getElementById(tab.dataset.tab).classList.remove('hidden');
            });
        });

        // Función para eliminar item del carrito
        function eliminarDelCarrito(itemId) {
            if (confirm('¿Estás seguro de que deseas eliminar este producto del carrito?')) {
                fetch('actualizar_carrito.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=remove&item_id=${itemId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error al eliminar el producto del carrito');
                    }
                });
            }
        }

        // Manejo del menú móvil
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Cambiar estilo del navbar al hacer scroll
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('main-nav');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        async function cargarCarrito() {
            try {
                const response = await fetch('get_carrito.php');
                const data = await response.json();
                
                if (data.success) {
                    const carritoItems = document.getElementById('carrito-items');
                    const totalCarrito = document.getElementById('total-carrito');
                    const btnFinalizar = document.getElementById('btn-finalizar');
                    
                    if (data.items.length === 0) {
                        carritoItems.innerHTML = '<p class="text-center text-gray-500">No hay productos en el carrito</p>';
                        btnFinalizar.disabled = true;
                        return;
                    }

                    btnFinalizar.disabled = false;
                    let html = '<div class="space-y-4">';
                    
                    data.items.forEach(item => {
                        html += `
                            <div class="flex items-center justify-between p-4 border rounded-lg">
                                <div class="flex-1">
                                    <h3 class="font-medium">${item.nombre}</h3>
                                    <p class="text-sm text-gray-500">Bs. ${item.precio_unitario} x ${item.cantidad}</p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="font-medium">Bs. ${item.subtotal}</span>
                                    <div class="flex items-center space-x-2">
                                        <button class="btn-cantidad" data-accion="restar" data-id="${item.id}">-</button>
                                        <span>${item.cantidad}</span>
                                        <button class="btn-cantidad" data-accion="sumar" data-id="${item.id}">+</button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                    carritoItems.innerHTML = html;
                    totalCarrito.textContent = data.total.toFixed(2);
                }
            } catch (error) {
                console.error('Error:', error);
                const carritoItems = document.getElementById('carrito-items');
                if (carritoItems) {
                    carritoItems.innerHTML = '<p class="text-center text-red-500">No se pudo cargar el carrito en este momento.</p>';
                }
            }
        }

        // Cargar carrito al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            cargarCarrito();

            // Manejar cambios de cantidad
            document.getElementById('carrito-items').addEventListener('click', async function(e) {
                if (e.target.classList.contains('btn-cantidad')) {
                    const id = e.target.dataset.id;
                    const accion = e.target.dataset.accion;
                    
                    try {
                        const response = await fetch('actualizar_carrito.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ id, accion })
                        });
                        
                        const data = await response.json();
                        if (data.success) {
                            cargarCarrito();
                        } else {
                            alert(data.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al actualizar cantidad');
                    }
                }
            });

            // Modal de finalización
            const modal = document.getElementById('modal-finalizar');
            const btnFinalizar = document.getElementById('btn-finalizar');
            const btnCancelar = document.getElementById('btn-cancelar');
            const formFinalizar = document.getElementById('form-finalizar-pedido');

            btnFinalizar.addEventListener('click', () => {
                modal.classList.remove('hidden');
            });

            btnCancelar.addEventListener('click', () => {
                modal.classList.add('hidden');
            });

            formFinalizar.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const datos = Object.fromEntries(formData.entries());

                try {
                    const response = await fetch('finalizar_pedido.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(datos)
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        alert('¡Pedido realizado con éxito!');
                        window.location.href = 'mi_cuenta.php?section=pedidos';
                    } else {
                        alert(data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al procesar el pedido');
                }
            });
        });
    </script>
</body>
</html> 