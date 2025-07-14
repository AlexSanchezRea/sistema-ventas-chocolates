<?php
require_once 'includes/session.php';
// if (!isLoggedIn()) {
//     header('Location: login.php');
//     exit;
// }
?><?php
require_once 'config/database.php';
require_once 'includes/session.php';

// Obtener cantidad de items en el carrito si el usuario está logueado
$itemsCarrito = 0;
if (isClient()) {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM detalles_pedido dp 
        JOIN pedidos p ON dp.pedido_id = p.id 
        WHERE p.usuario_id = ? AND p.estado = 'pendiente'
    ");
    $stmt->execute([getCurrentUserId()]);
    $itemsCarrito = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo | Sweet Mett</title>
    <link rel="stylesheet" href="css/tailwind.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Open+Sans:wght@400;700&family=Playfair+Display:wght@400;700&family=Pacifico&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .producto-card {
            background-color: white;
            transition: all 0.3s ease;
        }
        .producto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .producto-imagen {
            transition: transform 0.3s ease;
        }
        .producto-card:hover .producto-imagen {
            transform: scale(1.05);
        }
        .badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-new {
            background-color: #10B981;
            color: white;
        }
        .badge-popular {
            background-color: #EF4444;
            color: white;
        }
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
            display: <?php echo $itemsCarrito > 0 ? 'flex' : 'none'; ?>;
        }
        #carrito-flotante {
            transition: transform 0.3s ease, width 0.3s ease;
        }
        #carrito-flotante:hover {
            transform: translateX(0);
        }
        .carrito-item {
            animation: slideIn 0.3s ease;
        }
        .carrito-item .cantidad-control {
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .carrito-item:hover .cantidad-control {
            opacity: 1;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        .cantidad-control {
            display: inline-flex;
            align-items: center;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            padding: 2px;
        }
        .cantidad-control button {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: all 0.2s ease;
        }
        .cantidad-control button:hover {
            background: #e5e7eb;
        }
        .cantidad-control span {
            padding: 0 8px;
            min-width: 24px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body class="bg-amber-50 text-chocolate-darker min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav id="main-nav" class="fixed w-full z-50 text-white shadow-lg transition-all duration-500 ease-in-out">
        <div class="container mx-auto px-6 py-2 md:py-3 flex justify-between items-center transition-all duration-500">
            <div class="flex items-center">
                <a href="/" class="flex items-center">
                    <img src="assets/logo-blanco.png" width="70px" alt="logo de sweet mett" class="transition-all duration-500 logo-animate hover:opacity-80">
                </a>
            </div>

            <div class="hidden md:flex items-center space-x-6 font-medium">
                <a href="index.php#nosotros" class="hover:text-gold transition duration-300">Nosotros</a>
                <a href="index.php#productos" class="hover:text-gold transition duration-300">Productos</a>
                <a href="index.php#galeria" class="hover:text-gold transition duration-300">Galería</a>
                <a href="catalogo.php" class="hover:text-gold transition duration-300">Catálogo</a>
               
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="admin/dashboard.php" class="hover:text-gold transition duration-300">Panel Admin</a>
                    <?php else: ?>
                        <a href="cliente/mi_cuenta.php" class="hover:text-gold transition duration-300">Mi Cuenta</a>
                        <a href="cliente/mi_cuenta.php" class="hover:text-gold transition duration-300 relative">
                            <i class="fas fa-shopping-cart"></i>
                            <?php if ($itemsCarrito > 0): ?>
                                <span class="cart-count"><?php echo $itemsCarrito; ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="ml-4 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-1.5 px-5 rounded-full transition duration-300">
                        Cerrar Sesión
                    </a>
                <?php else: ?>
                    <a href="login.php" class="ml-4 bg-gold hover:bg-yellow-500 text-chocolate-darker text-sm font-semibold py-1.5 px-5 rounded-full transition duration-300">
                        Iniciar Sesión
                    </a>
                <?php endif; ?>
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
                <a href="index.php#nosotros" class="block py-2 hover:text-gold font-medium">Nosotros</a>
                <a href="index.php#productos" class="block py-2 hover:text-gold font-medium">Productos</a>
                <a href="index.php#galeria" class="block py-2 hover:text-gold font-medium">Galería</a>
                <a href="catalogo.php" class="block py-2 hover:text-gold font-medium">Catálogo</a>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="admin/dashboard.php" class="block py-2 hover:text-gold font-medium">Panel Admin</a>
                    <?php else: ?>
                        <a href="cliente/mi_cuenta.php" class="block py-2 hover:text-gold font-medium">Mi Cuenta</a>
                        <a href="cliente/mi_cuenta.php" class="block py-2 hover:text-gold font-medium">
                            Carrito
                            <?php if ($itemsCarrito > 0): ?>
                                <span class="ml-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs"><?php echo $itemsCarrito; ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="block py-2 text-red-400 hover:text-red-300 font-medium">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="login.php" class="block py-2 text-gold hover:text-yellow-400 font-medium">Iniciar Sesión</a>
                <?php endif; ?>
                <a href="https://wa.me/1234567890" class="block py-2 text-green-400 hover:text-green-300 font-medium">
                    <i class="fab fa-whatsapp mr-2"></i>Contacto
                </a>
            </div>
        </div>
    </nav>

    <!-- Carrito Flotante -->
    <div id="carrito-flotante" class="fixed top-24 right-4 z-50 bg-white rounded-lg shadow-xl transition-all duration-300 transform translate-x-full hover:translate-x-0 group">
        <div class="absolute left-0 top-0 bottom-0 w-6 bg-chocolate rounded-l-lg cursor-pointer flex items-center justify-center transform -translate-x-full">
            <i class="fas fa-shopping-cart text-white"></i>
            <span class="cart-count-float absolute -top-2 -left-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center" style="display: none;">0</span>
        </div>
        
        <div class="w-72 max-h-[80vh] flex flex-col">
            <div class="p-4 bg-chocolate text-white rounded-t-lg">
                <h3 class="font-semibold text-lg">Tu Carrito</h3>
            </div>

            <div id="items-carrito" class="flex-1 overflow-y-auto p-4 space-y-3">
                <!-- Los items se cargarán aquí dinámicamente -->
            </div>

            <div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                <div class="flex justify-between items-center mb-3">
                    <span class="font-medium">Total:</span>
                    <span class="text-lg font-bold text-gold" id="total-carrito">Bs. 0.00</span>
                </div>
                <a href="cliente/finalizar_pedido.php" class="block w-full bg-gold text-chocolate-darker text-center py-2 rounded-full font-semibold hover:bg-yellow-500 transition text-sm">
                    Finalizar Compra
                </a>
            </div>
        </div>
    </div>

    <main class="container mx-auto px-6 py-24 flex-1">
        <h1 class="text-4xl font-pacifico text-gold mb-2 text-center">Nuestro Catálogo</h1>
        <p class="text-center text-gray-600 mb-12">Descubre nuestra deliciosa selección de chocolates artesanales</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php
            // Obtener productos de la base de datos
            $database = new Database();
            $db = $database->getConnection();
            
            $stmt = $db->prepare("SELECT * FROM productos WHERE stock > 0");
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Arreglo de imágenes disponibles en assets
            $imagenes_disponibles = [
                'galeria1.webp',
                'galeria2.webp',
                'galeria3.webp',
                'frutos.webp',
                'leche.webp',
                'negro.webp',
                'blanco.webp',
                'poster.webp',
                'utepsa.webp',
            ];
            $producto_index = 0;
            ?>
            <?php
            foreach ($productos as $producto):
                // Seleccionar imagen
                $img = !empty($producto['imagen']) ? $producto['imagen'] : $imagenes_disponibles[$producto_index % count($imagenes_disponibles)];
                if (!file_exists(__DIR__ . '/assets/' . $img)) {
                    $img = $imagenes_disponibles[$producto_index % count($imagenes_disponibles)];
                }
            ?>
            <div class="producto-card rounded-xl shadow-lg p-6 flex flex-col items-center relative">
                <?php if (isset($producto['es_nuevo']) && $producto['es_nuevo']): ?>
                    <span class="badge badge-new">Nuevo</span>
                <?php endif; ?>
                <?php if (isset($producto['es_popular']) && $producto['es_popular']): ?>
                    <span class="badge badge-popular">Popular</span>
                <?php endif; ?>
                
                <div class="w-48 h-48 overflow-hidden rounded-lg mb-4">
                    <img src="assets/<?php echo htmlspecialchars($img); ?>"
                         alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                         class="w-full h-full object-cover producto-imagen">
                </div>
                
                <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                <p class="text-gold font-bold mb-2">Bs. <?php echo number_format($producto['precio'], 2); ?></p>
                <p class="text-gray-600 text-center text-sm mb-4"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                
                <?php if (isClient()): ?>
                    <button class="btn-comprar w-full bg-gold hover:bg-yellow-500 text-chocolate-darker font-semibold py-2 px-4 rounded-full transition duration-300 flex items-center justify-center"
                            data-producto-id="<?php echo $producto['id']; ?>">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Comprar
                    </button>
                <?php else: ?>
                    <a href="login.php" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-full transition duration-300 text-center">
                        Iniciar sesión para comprar
                    </a>
                <?php endif; ?>
            </div>
            <?php $producto_index++; endforeach; ?>
        </div>
    </main>

    <footer class="bg-chocolate text-white py-8 mt-16">
        <div class="container mx-auto px-6">
            <div class="text-center">
                <p class="mb-4">¿Tienes alguna pregunta? Contáctanos:</p>
                <a href="https://wa.me/1234567890" class="inline-flex items-center text-gold hover:text-yellow-400 transition">
                    <i class="fab fa-whatsapp text-2xl mr-2"></i>
                    Escríbenos por WhatsApp
                </a>
            </div>
        </div>
    </footer>

    <script>
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

        // Funcionalidad del carrito
        const carrito = {
            items: [],
            total: 0,
            
            agregarProducto(id, nombre, precio, imagen) {
                const itemExistente = this.items.find(item => item.id === id);
                
                if (itemExistente) {
                    itemExistente.cantidad++;
                } else {
                    this.items.push({
                        id: id,
                        nombre: nombre,
                        precio: precio,
                        imagen: imagen,
                        cantidad: 1
                    });
                }
                
                this.actualizarCarrito();
                this.guardarEnServidor();
                this.mostrarNotificacion(`${nombre} agregado al carrito`);
            },
            
            eliminarProducto(id) {
                this.items = this.items.filter(item => item.id !== id);
                this.actualizarCarrito();
                this.guardarEnServidor();
            },
            
            actualizarCantidad(id, cantidad) {
                const item = this.items.find(item => item.id === id);
                if (item) {
                    item.cantidad = Math.max(1, cantidad);
                    this.actualizarCarrito();
                    this.guardarEnServidor();
                }
            },
            
            calcularTotal() {
                return this.items.reduce((total, item) => total + (item.precio * item.cantidad), 0);
            },
            
            actualizarCarrito() {
                const contenedor = document.getElementById('items-carrito');
                contenedor.innerHTML = '';
                
                this.items.forEach(item => {
                    const itemHTML = `
                        <div class="carrito-item flex items-start gap-2 bg-white p-2 rounded-lg shadow-sm">
                            <img src="${item.imagen}" alt="${item.nombre}" class="w-12 h-12 object-cover rounded">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-sm truncate">${item.nombre}</h4>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-gold text-sm">Bs. ${(item.precio * item.cantidad).toFixed(2)}</span>
                                    <div class="cantidad-control">
                                        <button onclick="carrito.actualizarCantidad(${item.id}, ${item.cantidad - 1})">-</button>
                                        <span>${item.cantidad}</span>
                                        <button onclick="carrito.actualizarCantidad(${item.id}, ${item.cantidad + 1})">+</button>
                                    </div>
                                </div>
                            </div>
                            <button onclick="carrito.eliminarProducto(${item.id})" class="text-red-500 hover:text-red-700 text-sm">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    contenedor.innerHTML += itemHTML;
                });
                
                // Actualizar total
                document.getElementById('total-carrito').textContent = `Bs. ${this.calcularTotal().toFixed(2)}`;
                
                // Actualizar contador flotante
                const cartCountFloat = document.querySelector('.cart-count-float');
                const totalItems = this.items.reduce((total, item) => total + item.cantidad, 0);
                
                if (totalItems > 0) {
                    cartCountFloat.textContent = totalItems;
                    cartCountFloat.style.display = 'flex';
                } else {
                    cartCountFloat.style.display = 'none';
                }
                
                // Actualizar contador del navbar
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    if (totalItems > 0) {
                        cartCount.textContent = totalItems;
                        cartCount.style.display = 'block';
                    } else {
                        cartCount.style.display = 'none';
                    }
                }
            },
            
            mostrarNotificacion(mensaje) {
                const notificacion = document.createElement('div');
                notificacion.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300';
                notificacion.textContent = mensaje;
                document.body.appendChild(notificacion);
                
                // Mostrar notificación
                setTimeout(() => {
                    notificacion.style.transform = 'translateY(0)';
                    notificacion.style.opacity = '1';
                }, 100);
                
                // Ocultar y eliminar notificación
                setTimeout(() => {
                    notificacion.style.transform = 'translateY(full)';
                    notificacion.style.opacity = '0';
                    setTimeout(() => notificacion.remove(), 300);
                }, 2000);
            },
            
            async guardarEnServidor() {
                if (!isClient) return;
                
                try {
                    const response = await fetch('cliente/actualizar_carrito.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(this.items)
                    });
                    
                    if (!response.ok) throw new Error('Error al guardar el carrito');
                    
                } catch (error) {
                    console.error('Error:', error);
                }
            },
            
            async cargarDelServidor() {
                if (!isClient) return;
                
                try {
                    const response = await fetch('cliente/get_carrito.php');
                    if (!response.ok) throw new Error('Error al cargar el carrito');
                    
                    const data = await response.json();
                    this.items = data;
                    this.actualizarCarrito();
                    
                } catch (error) {
                    console.error('Error:', error);
                }
            }
        };

        // Inicializar carrito al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            const botonesComprar = document.querySelectorAll('.btn-comprar');
            
            // Cargar carrito del servidor
            carrito.cargarDelServidor();
            
            // Event listeners para botones de comprar
            botonesComprar.forEach(boton => {
                boton.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (!isClient) {
                        window.location.href = 'login.php';
                        return;
                    }
                    
                    const producto = e.target.closest('.producto-card');
                    const id = parseInt(producto.dataset.productoId);
                    const nombre = producto.dataset.nombre;
                    const precio = parseFloat(producto.dataset.precio);
                    const imagen = producto.dataset.imagen;
                    
                    carrito.agregarProducto(id, nombre, precio, imagen);
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Botón finalizar compra
            const btnFinalizarCompra = document.querySelector('#btnFinalizarCompra');
            if (btnFinalizarCompra) {
                btnFinalizarCompra.addEventListener('click', function() {
                    window.location.href = 'cliente/mi_cuenta.php?section=carrito';
                });
            }

            // Botones de comprar producto
            document.querySelectorAll('.btn-comprar').forEach(btn => {
                btn.addEventListener('click', async function(e) {
                    e.preventDefault();
                    
                    // Mostrar indicador de carga
                    this.disabled = true;
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Agregando...';
                    
                    try {
                        const response = await fetch('cliente/agregar_carrito.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                producto_id: this.dataset.productoId
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Actualizar contador del carrito
                            const contadorCarrito = document.querySelector('.cart-count');
                            const contadorCarritoFloat = document.querySelector('.cart-count-float');
                            if (contadorCarrito) {
                                contadorCarrito.textContent = data.items_carrito;
                                contadorCarrito.style.display = 'flex';
                            }
                            if (contadorCarritoFloat) {
                                contadorCarritoFloat.textContent = data.items_carrito;
                                contadorCarritoFloat.style.display = 'flex';
                            }
                            
                            // Mostrar notificación de éxito
                            this.innerHTML = '<i class="fas fa-check mr-2"></i>¡Agregado!';
                            setTimeout(() => {
                                this.innerHTML = originalText;
                                this.disabled = false;
                            }, 2000);
                            
                            // Actualizar carrito flotante
                            actualizarCarritoFlotante();
                        } else {
                            // Mostrar error
                            alert(data.message);
                            this.innerHTML = originalText;
                            this.disabled = false;
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al agregar el producto al carrito');
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }
                });
            });

            // Función para actualizar el carrito flotante
            async function actualizarCarritoFlotante() {
                try {
                    const response = await fetch('cliente/get_carrito.php');
                    const data = await response.json();
                    
                    if (data.success) {
                        const itemsCarrito = document.getElementById('items-carrito');
                        const totalCarrito = document.getElementById('total-carrito');
                        
                        if (!data.items || data.items.length === 0) {
                            itemsCarrito.innerHTML = '<p class="text-center text-gray-500">No hay productos en el carrito</p>';
                            if (totalCarrito) totalCarrito.textContent = 'Bs. 0.00';
                            return;
                        }

                        let html = '';
                        data.items.forEach(item => {
                            html += `
                                <div class="carrito-item flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="font-medium">${item.nombre}</h4>
                                        <p class="text-sm text-gray-500">Bs. ${item.precio_unitario} x ${item.cantidad}</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium">Bs. ${item.subtotal}</span>
                                        <div class="cantidad-control">
                                            <button class="btn-cantidad" data-id="${item.id}" data-accion="restar">-</button>
                                            <span>${item.cantidad}</span>
                                            <button class="btn-cantidad" data-id="${item.id}" data-accion="sumar">+</button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        itemsCarrito.innerHTML = html;
                        if (totalCarrito) totalCarrito.textContent = `Bs. ${data.total.toFixed(2)}`;
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            }

            // Actualizar carrito flotante al cargar la página
            if (document.getElementById('items-carrito')) {
                actualizarCarritoFlotante();
            }

            // Manejar cambios de cantidad en el carrito flotante
            document.getElementById('items-carrito')?.addEventListener('click', async function(e) {
                if (e.target.classList.contains('btn-cantidad')) {
                    const id = e.target.dataset.id;
                    const accion = e.target.dataset.accion;
                    
                    try {
                        const response = await fetch('cliente/actualizar_carrito.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ id, accion })
                        });
                        
                        const data = await response.json();
                        if (data.success) {
                            actualizarCarritoFlotante();
                            
                            // Actualizar contadores
                            const contadorCarrito = document.querySelector('.cart-count');
                            const contadorCarritoFloat = document.querySelector('.cart-count-float');
                            if (contadorCarrito) {
                                contadorCarrito.textContent = data.items_carrito;
                                contadorCarrito.style.display = data.items_carrito > 0 ? 'flex' : 'none';
                            }
                            if (contadorCarritoFloat) {
                                contadorCarritoFloat.textContent = data.items_carrito;
                                contadorCarritoFloat.style.display = data.items_carrito > 0 ? 'flex' : 'none';
                            }
                        } else {
                            alert(data.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al actualizar cantidad');
                    }
                }
            });
        });
    </script>
</body>
</html>