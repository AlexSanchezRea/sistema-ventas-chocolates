<?php
require_once 'config/database.php';
require_once 'includes/session.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Mett</title>

    <!-- Preconexiones y precargas -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Fuentes Google optimizadas -->
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Open+Sans:wght@400;700&family=Playfair+Display:wght@400;700&family=Pacifico&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome optimizado -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <link href="css/tailwind.min.css" rel="stylesheet">
    
    <style>
        .bg-chocolate { background-color: #3A2314; }
        .bg-chocolate-darker { background-color: #2B1810; }
        .hover\:bg-chocolate-dark:hover { background-color: #4A2D1A; }
        .text-chocolate { color: #3A2314; }
        .text-chocolate-darker { color: #2B1810; }
        .border-chocolate { border-color: #3A2314; }
        .text-gold { color: #D4AF37; }
        .bg-gold { background-color: #D4AF37; }
        .hover\:bg-gold:hover { background-color: #C4A137; }
        
        .hero-pattern {
            background-color: #3A2314;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .video-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }

        .hero-overlay {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
        }

        .logo-animate {
            transition: all 0.3s ease;
        }

        .logo-animate:hover {
            transform: scale(1.05);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        [data-aos] {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }

        [data-aos].aos-animate {
            opacity: 1;
            transform: translateY(0);
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .gallery-item {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .gallery-item:hover {
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .gallery-item img {
            transition: transform 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        /* Ajustes específicos para el fondo y colores */
        #galeria, footer {
            background-color: #3B2213;
        }

        .font-pacifico {
            font-family: 'Pacifico', cursive;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        footer {
            background-color: #2B1810;
        }
        
        /* Ajustes específicos para el input y botón de suscripción */
        input[type="email"] {
            height: 45px;
        }

        button[type="submit"] {
            height: 45px;
        }
        #menu {
  position: absolute;
  top: 0; left: 0;
  width: 800px;
  height: 400px;
  background: rgba(59,34,19,0.85); /* Café con transparencia */
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 10;
  border-radius: 10px;
}
#menu h1 {
  color: #fff;
  font-size: 2.5rem;
  margin-bottom: 2rem;
  font-family: 'Pacifico', cursive;
  letter-spacing: 2px;
}
#menu button {
  font-size: 1.5rem;
  padding: 1rem 2.5rem;
  margin: 1rem;
  border: none;
  border-radius: 8px;
  background: #D4AF37;
  color: #3B2213;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s, transform 0.2s;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
#menu button:hover {
  background: #bfa133;
  transform: scale(1.05);
}

        /* Ajustes para los botones flotantes */
        .fab.fa-whatsapp {
            font-size: 1.75rem;
            color: green;
        }

        .producto-card {
            background-color: white;
            transition: all 0.3s ease;
        }

        .producto-card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Ajustes para la sección de productos */
        section[style*="background-color: #FDF6E9"] {
            background-color: #FDF6E9 !important;
        }

        .product-option {
            border: 1px solid rgba(255, 215, 0, 0.1);
            background-color: white;
        }

        .product-option:hover {
            border-color: #FFD700;
            background-color: #FFF8E7;
        }

        .product-option.active {
            border-color: #FFD700;
            background-color: #FFF8E7;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #productos {
            background-color: #FEF9F0;
        }

        #choco-image {
            border: 2px solid #FFD700;
        }

        .product-option.active {
            border: 2px solid #FFD700;
        }

        #main-nav {
            background-color: rgba(65, 45, 35, 0.7) !important;
            backdrop-filter: blur(5px);
        }
        #main-nav.scrolled {
            background-color: rgba(65, 45, 35, 0.85) !important;
            backdrop-filter: blur(8px);
        }
    </style>
</head>

<body class="bg-amber-50 text-chocolate-darker">
    <!-- Navbar -->
    <nav id="main-nav" class="fixed w-full z-50 text-white shadow-lg transition-all duration-500 ease-in-out" style="background-color: rgba(65, 45, 35, 0.7); backdrop-filter: blur(5px);">
        <div class="container mx-auto px-6 py-2 md:py-3 flex justify-between items-center transition-all duration-500">
            <div class="flex items-center">
                <a href="/" class="flex items-center">
                    <img src="assets/logo-blanco.png" width="70px" alt="logo de sweet mett" class="transition-all duration-500 logo-animate hover:opacity-80">
                </a>
            </div>

            <div class="hidden md:flex items-center space-x-6 font-medium">
                <a href="#nosotros" class="hover:text-gold transition duration-300">Nosotros</a>
                <a href="#productos" class="hover:text-gold transition duration-300">Productos</a>
                <a href="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? '#galeria' : 'index.php#galeria'; ?>" class="hover:text-gold transition duration-300">Galería</a>
                <a href="catalogo.php" class="hover:text-gold transition duration-300">Catálogo</a>
           
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="admin/dashboard.php" class="hover:text-gold transition duration-300">Panel Admin</a>
                    <?php else: ?>
                        <a href="cliente/mi_cuenta.php" class="hover:text-gold transition duration-300">Mi Cuenta</a>
                        <a href="cliente/mi_cuenta.php" class="hover:text-gold transition duration-300">
                            <i class="fas fa-shopping-cart"></i>
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="ml-4 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-1.5 px-5 rounded-full transition duration-300">
                        Cerrar Sesión
                    </a>
                <?php else: ?>
                    <a href="login.php"
                       class="border-2 border-gold text-gold hover:bg-gold hover:text-chocolate-darker transition duration-300 rounded-full px-5 py-1.5 text-sm font-semibold flex items-center gap-2">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                    </a>
                    <a href="registro.php"
                       class="ml-4 bg-gold hover:bg-yellow-500 text-chocolate-darker text-sm font-semibold py-1.5 px-5 rounded-full shadow-lg transition duration-300 transform hover:scale-105 flex items-center gap-2">
                        <i class="fas fa-user-plus"></i> Registrarse
                    </a>
                <?php endif; ?>
                <a href="https://wa.me/+59175018448" class="ml-4 bg-[#25D366] hover:bg-[#128C7E] text-white text-sm font-semibold py-1.5 px-5 rounded-full transition duration-300 transform hover:scale-[1.03] active:scale-95 flex items-center">
                    <i class="fab fa-whatsapp mr-2"></i> Contacto
                </a>
            </div>

            <div class="md:hidden">
                <button class="mobile-menu-button focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Menú Móvil -->
        <div class="mobile-menu hidden md:hidden bg-chocolate-dark/95 py-3 px-6">
            <a href="#nosotros" class="block py-2 hover:text-gold font-medium">Nosotros</a>
            <a href="#productos" class="block py-2 hover:text-gold font-medium">Productos</a>
            <a href="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? '#galeria' : 'index.php#galeria'; ?>" class="block py-2 hover:text-gold font-medium">Galería</a>
            <a href="catalogo.php" class="block py-2 hover:text-gold font-medium">Catálogo</a>
            <a href="jueguito.php" class="block py-2 hover:text-gold font-medium">Jueguito</a>
            <?php if (isLoggedIn()): ?>
                <?php if (isAdmin()): ?>
                    <a href="admin/dashboard.php" class="block py-2 hover:text-gold font-medium">Panel Admin</a>
                <?php else: ?>
                    <a href="cliente/mi_cuenta.php" class="block py-2 hover:text-gold font-medium">Mi Cuenta</a>
                    <a href="cliente/mi_cuenta.php" class="block py-2 hover:text-gold font-medium">Carrito</a>
                <?php endif; ?>
                <a href="logout.php" class="block py-2 text-red-400 hover:text-red-300 font-medium">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php"
                   class="block py-2 mt-2 border-2 border-gold text-gold hover:bg-gold hover:text-chocolate-darker transition duration-300 rounded-full text-center font-semibold flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </a>
                <a href="registro.php"
                   class="block py-2 mt-2 bg-gold hover:bg-yellow-500 text-chocolate-darker font-semibold rounded-full text-center shadow-lg transition duration-300 transform hover:scale-105 flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus"></i> Registrarse
                </a>
            <?php endif; ?>
            <a href="https://wa.me/+59175018448" class="block py-2 text-[#25D366] hover:text-[#128C7E] font-medium">Contacto</a>
        </div>
    </nav>

    <!-- Hero Section con Video Background -->
    <section class="relative h-screen flex items-center justify-center overflow-hidden">
        <video autoplay muted loop class="video-background">
            <source src="assets/video1.mp4" type="video/mp4">
        </video>

        <div class="hero-overlay absolute inset-0"></div>

        <div class="container mx-auto px-6 z-10 text-center text-white" data-aos="fade-up">
            <h1 class="font-pacifico text-4xl md:text-6xl lg:text-7xl mb-6">
                <span class="block">¡Dulzura</span>
                <span class="block text-gold">pa' chuparse los dedos!</span>
            </h1>

            <p class="text-xl md:text-2xl max-w-2xl mx-auto mb-8">
                El nombre científico del cacao significa "alimento de los dioses", y nosotros lo preparamos al puro estilo cruceño.
            </p>

            <a href="#productos"
                class="inline-block bg-gold hover:bg-yellow-600 text-chocolate-darker font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105">
                Conoce nuestros productos
            </a>
        </div>
    </section>

    <!-- Sección Nosotros -->
    <section id="nosotros" class="relative py-20 bg-chocolate-dark text-white" style="background: url('assets/background.webp') fixed center/cover no-repeat; min-height: 100vh;">
        <!-- Contenido -->
        <div class="container mx-auto px-6 relative z-10">
            <div class="max-w-4xl mx-auto bg-black bg-opacity-60 p-10 rounded-lg" data-aos="fade-right">
                <h2 class="text-3xl md:text-4xl mb-6 text-gold font-pacifico">Nuestra Historia</h2>
                <p class="mb-6 text-lg">
                    SweetMett es un emprendimiento independiente creado en 2024 por estudiantes de la Universidad UTEPSA de Santa Cruz de la Sierra. Nos especializamos en chocolates artesanales de alta calidad, donde combinamos innovación y tradición en cada creación.
                </p>

                <div class="flex justify-center my-6">
                    <img src="assets/utepsa.webp" alt="universidad utepsa" class="rounded-lg w-full max-w-xs md:max-w-sm border-2 border-gold">
                </div>

                <p class="mt-6 text-lg">
                    Como jóvenes emprendedores, hemos fusionado nuestros conocimientos académicos con la pasión por la chocolatería, seleccionando cuidadosamente los mejores ingredientes locales.
                </p>
            </div>
        </div>
    </section>

    <!-- Sección Productos - Versión Mobile Friendly -->
    <section id="productos" class="py-12 md:py-20" style="background-color: #FEF9F0;">
        <div class="container mx-auto px-4 sm:px-6">
            <!-- Encabezado -->
            <div class="text-center mb-10 md:mb-16" data-aos="fade-up">
                <h2 class="font-pacifico text-2xl sm:text-3xl md:text-4xl mb-3 md:mb-4 text-[#2B1810]">Nuestros Productos</h2>
            </div>

            <div class="flex flex-col md:flex-row items-center">
                <!-- Imagen del producto (primera en móvil) -->
                <div class="w-full md:w-1/2 mb-6 md:mb-0 md:pr-8 lg:pr-10 order-1 md:order-none" data-aos="fade-right">
                    <div class="relative">
                        <div id="choco-image" class="w-full h-64 sm:h-80 md:h-96 rounded-2xl shadow-lg transition-all duration-500 border-4 border-[#FFD700] overflow-hidden">
                            <img src="assets/leche.webp" alt="Chocolate" class="w-full h-full object-cover">
                        </div>
                        <div id="choco-price" class="absolute bottom-8 right-8 bg-white px-6 py-3 rounded-full shadow-lg">
                            <span class="text-xl sm:text-2xl font-bold text-[#FFD700]">2.50Bs</span>
                        </div>
                    </div>
                </div>

                <!-- Contenido (segundo en móvil) -->
                <div class="w-full md:w-1/2 order-2 md:order-none" data-aos="fade-left">
                    <p class="text-base sm:text-lg mb-8 text-center md:text-left text-[#2B1810]">
                        Cada pieza es el fruto de nuestro amor, pasión y dedicación por el chocolate.
                    </p>

                    <!-- Grid de productos responsive -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="product-option bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer border-4 border-[#FFD700] overflow-hidden"
                            data-image="assets/leche.webp"
                            data-price="2.50">
                            <div class="p-4">
                                <h4 class="text-[#2B1810] text-lg font-medium">Chocolate con leche</h4>
                                <p class="text-[#FFD700] font-bold mt-2">2.50Bs</p>
                            </div>
                        </div>

                        <div class="product-option bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer border-4 border-[#FFD700] overflow-hidden"
                            data-image="assets/negro.webp"
                            data-price="3.00">
                            <div class="p-4">
                                <h4 class="text-[#2B1810] text-lg font-medium">Chocolate amargo</h4>
                                <p class="text-[#FFD700] font-bold mt-2">3.00Bs</p>
                            </div>
                        </div>

                        <div class="product-option bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer border-4 border-[#FFD700] overflow-hidden"
                            data-image="assets/blanco.webp"
                            data-price="3.00">
                            <div class="p-4">
                                <h4 class="text-[#2B1810] text-lg font-medium">Chocolate blanco</h4>
                                <p class="text-[#FFD700] font-bold mt-2">3.00Bs</p>
                            </div>
                        </div>

                        <div class="product-option bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer border-4 border-[#FFD700] overflow-hidden"
                            data-image="assets/frutos.webp"
                            data-price="3.00">
                            <div class="p-4">
                                <h4 class="text-[#2B1810] text-lg font-medium">Chocolate con nueces</h4>
                                <p class="text-[#FFD700] font-bold mt-2">3.00Bs</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Galería Section -->
    <section id="galeria" class="py-20" style="background-color: #3B2213;">
        <div class="container mx-auto px-6">
            <h2 class="font-pacifico text-4xl mb-16 text-center text-white">Galería</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="gallery-item overflow-hidden rounded-xl" data-aos="fade-up">
                    <img src="assets/galeria1.webp" alt="Chocolates variados" class="w-full h-64 object-cover transform transition-transform duration-500 hover:scale-110">
                </div>
                <div class="gallery-item overflow-hidden rounded-xl" data-aos="fade-up" data-aos-delay="100">
                    <img src="assets/galeria2.webp" alt="Bombones de chocolate" class="w-full h-64 object-cover transform transition-transform duration-500 hover:scale-110">
                </div>
                <div class="gallery-item overflow-hidden rounded-xl" data-aos="fade-up" data-aos-delay="200">
                    <img src="assets/galeria3.webp" alt="Chocolate derretido" class="w-full h-64 object-cover transform transition-transform duration-500 hover:scale-110">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Juego debajo de la galería -->
    <section id="juego" class="flex flex-col items-center justify-center py-16 bg-transparent" style="position:relative;">
        <div style="position:relative; width:800px; height:400px;">
            <canvas id="game" width="800" height="400"></canvas>
            <div id="menu">
                <h1>Sweet Melt</h1>
                <button id="btn-iniciar" onclick="startGame()">Iniciar Juego</button>
                <button id="btn-reanudar" onclick="reanudarJuego()">Reanudar</button>
            </div>
        </div>
    </section>
    <script src="game.js"></script>

    <!-- Contact & Footer Section -->
    <footer class="bg-chocolate-darker text-white pt-20 pb-10">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between mb-16">
                <div class="md:w-1/3 mb-10 md:mb-0" data-aos="fade-up">
                    <a href="/" class="flex items-center">
                        <img src="assets/logo-blanco.png" width="80px" alt="logo de sweet mett" class="transition-all duration-500 logo-animate hover:opacity-80">
                    </a>

                    <div class="flex space-x-4 mt-4">
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-chocolate flex items-center justify-center hover:bg-gold transition duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-chocolate flex items-center justify-center hover:bg-gold transition duration-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>

                <div class="md:w-1/3 mb-10 md:mb-0" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="font-pacifico text-2xl mb-6">Contacto</h3>
                    <address class="not-italic">
                        <p class="mb-3"><i class="fas fa-map-marker-alt mr-3 text-gold"></i> Santa Cruz de la Sierra</p>
                        <p class="mb-3"><i class="fas fa-phone mr-3 text-gold"></i>+591 75018448</p>
                        <p class="mb-3"><i class="fas fa-envelope mr-3 text-gold"></i> ejemplo@gmail.com</p>
                    </address>
                </div>

                <div class="md:w-1/3" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="font-pacifico text-2xl mb-6">Subscríbete para ofertas</h3>
                    <form class="mb-6">
                        <div class="flex">
                            <input type="email" placeholder="Tu correo"
                                class="px-4 py-2 w-full rounded-l-lg focus:outline-none text-gray-800">
                            <button type="submit"
                                class="bg-gold hover:bg-yellow-600 text-chocolate-darker font-bold px-4 rounded-r-lg transition duration-300">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                    <p class="text-sm">Recibe nuestras promociones especiales</p>
                </div>
            </div>

            <div class="border-t border-chocolate pt-6 text-center">
                <p>&copy; 2025 Sweet Mett. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Button -->
    <div class="fixed bottom-8 right-8 z-50">
        <a href="https://wa.me/+59175018448"
            class="flex items-center gap-2 bg-chocolate-darker text-white px-4 py-2 rounded-full text-sm font-medium transition duration-300">
            <i class="fab fa-whatsapp text-white"></i>
            
        </a>
    </div>

    <!-- Scroll to Top Button -->
    <button id="scrollToTop"
        class="fixed bottom-24 right-8 bg-chocolate text-white w-12 h-12 rounded-full flex items-center justify-center text-xl shadow-lg hover:bg-chocolate-dark transition duration-300 z-50 hidden">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Inicializar AOS (Animate On Scroll)
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Smooth scroll para los enlaces de navegación
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                // Solo prevenir si el enlace es un hash y estamos en index.php
                if (window.location.pathname.endsWith('index.php') && href.startsWith('#')) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        // Cerrar menú móvil si está abierto
                        document.querySelector('.mobile-menu').classList.add('hidden');
                    }
                }
            });
        });

        // Animación del navbar al hacer scroll
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('main-nav');
            const scrollToTopBtn = document.getElementById('scrollToTop');
            
            if (window.scrollY > 50) {
                nav.classList.add('py-2');
                nav.classList.remove('py-4');
                scrollToTopBtn.classList.remove('hidden');
            } else {
                nav.classList.add('py-4');
                nav.classList.remove('py-2');
                scrollToTopBtn.classList.add('hidden');
            }
        });

        // Menú móvil
        document.querySelector('.mobile-menu-button').addEventListener('click', function() {
            document.querySelector('.mobile-menu').classList.toggle('hidden');
        });

        // Botón de scroll to top
        document.getElementById('scrollToTop').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Asegurarse de que las imágenes estén cargadas
        window.addEventListener('DOMContentLoaded', (event) => {
            // Verificar si la imagen principal existe
            const mainImage = document.querySelector('.rounded-2xl img');
            if (mainImage) {
                mainImage.onerror = function() {
                    this.src = 'assets/galeria1.webp'; // Imagen de respaldo
                };
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const productOptions = document.querySelectorAll('.product-option');
            const chocoImage = document.querySelector('#choco-image img');
            const chocoPrice = document.querySelector('#choco-price span');

            productOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Actualizar imagen y precio
                    const image = this.getAttribute('data-image');
                    const price = this.getAttribute('data-price');
                    
                    chocoImage.src = image;
                    chocoPrice.textContent = price + 'Bs';

                    // Actualizar estados activos
                    productOptions.forEach(p => p.classList.remove('active'));
                    this.classList.add('active');

                    // Efecto de transición
                    chocoImage.style.opacity = '0';
                    setTimeout(() => {
                        chocoImage.style.opacity = '1';
                    }, 200);
                });
            });
        });

        // Cerrar menú móvil al hacer clic en cualquier enlace
document.querySelectorAll('.mobile-menu a').forEach(link => {
    link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        // Si NO es un hash, cerrar menú y dejar que navegue normalmente
        if (!href.startsWith('#')) {
            document.querySelector('.mobile-menu').classList.add('hidden');
        }
    });
});
    </script>
</body>

</html>