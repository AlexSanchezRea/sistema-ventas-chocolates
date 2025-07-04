document.addEventListener('DOMContentLoaded', function() {
    // Configuración de Tailwind
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    chocolate: {
                        light: '#D2B48C',
                        DEFAULT: '#6B4423',
                        dark: '#4E3524',
                        darker: '#3C2415'
                    },
                    gold: '#D4AF37'
                },
                fontFamily: {
                    sans: ['"Open Sans"', 'sans-serif'],
                    serif: ['"Playfair Display"', 'serif'],
                    script: ['"Dancing Script"', 'cursive']
                }
            }
        }
    };

    // Inicializar AOS
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });

    // Elementos del DOM
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const mobileMenu = document.querySelector('.mobile-menu');
    const scrollToTopButton = document.getElementById('scrollToTop');
    const parallax = document.querySelector('.parallax');
    const productCards = document.querySelectorAll('.product-card');
    const productOptions = document.querySelectorAll('.product-option');
    const chocoImage = document.getElementById('choco-image');
    const chocoPrice = document.getElementById('choco-price');
    const nav = document.getElementById('main-nav');
    const logo = document.querySelector('nav img');
    const mainImage = document.querySelector('#choco-image img');
    const priceDisplay = document.querySelector('#choco-price p');
    const productItems = document.querySelectorAll('.flex.justify-between');

    // Menú móvil
    function toggleMobileMenu() {
        mobileMenu.classList.toggle('hidden');
    }

    // Scroll suave
    function smoothScroll(target) {
        window.scrollTo({
            top: target.offsetTop - 80,
            behavior: 'smooth'
        });
    }

    // Efecto hover en tarjetas de producto
    function handleCardHover(e) {
        const x = e.clientX - this.getBoundingClientRect().left;
        const y = e.clientY - this.getBoundingClientRect().top;
        const centerX = this.offsetWidth / 2;
        const centerY = this.offsetHeight / 2;
        const angleX = (y - centerY) / 20;
        const angleY = (centerX - x) / 20;

        this.style.transform = `perspective(1000px) rotateX(${angleX}deg) rotateY(${angleY}deg) translateY(-10px)`;
    }

    function resetCardHover() {
        this.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0)';
    }

    // Cambiar producto seleccionado
    function actualizarProducto(element) {
        // Obtener la imagen y el precio del producto clickeado
        const imagen = element.getAttribute('data-image');
        const precio = element.querySelector('.text-[#FFEB3B]').textContent;
        
        // Actualizar la imagen principal
        const imagenPrincipal = document.querySelector('#choco-image img');
        if (imagenPrincipal) {
            imagenPrincipal.src = imagen;
        }
        
        // Actualizar el precio flotante
        const precioFlotante = document.querySelector('#choco-price p');
        if (precioFlotante) {
            precioFlotante.textContent = precio;
        }
    }

    // Efecto de scroll en navbar
    function handleNavbarScroll() {
        if (window.scrollY > 50) {
            nav.classList.remove('bg-[#2B1810]/50', 'py-0');
            nav.classList.add('bg-[#2B1810]', 'py-1');
            logo.style.width = '70px';
        } else {
            nav.classList.add('bg-[#2B1810]/50', 'py-0');
            nav.classList.remove('bg-[#2B1810]', 'py-1');
            logo.style.width = '60px';
        }
    }

    // Efecto parallax
    function handleParallax() {
        if (parallax) {
            const scrollPosition = window.pageYOffset;
            parallax.style.backgroundPositionY = scrollPosition * 0.5 + 'px';
        }
    }

    // Mostrar/ocultar botón de scroll to top
    function handleScrollToTopButton() {
        if (window.pageYOffset > 300) {
            scrollToTopButton.classList.remove('hidden');
        } else {
            scrollToTopButton.classList.add('hidden');
        }
    }

    // Event Listeners
    mobileMenuButton.addEventListener('click', toggleMobileMenu);

    scrollToTopButton.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                smoothScroll(targetElement);
                
                if (!mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }
            }
        });
    });

    productCards.forEach(card => {
        card.addEventListener('mousemove', handleCardHover);
        card.addEventListener('mouseleave', resetCardHover);
    });

    productItems.forEach(item => {
        item.addEventListener('click', function() {
            const price = this.querySelector('.text-[#FFEB3B]').textContent;
            const productName = this.querySelector('h4').textContent;
            let imagePath;
            
            switch(productName.toLowerCase()) {
                case 'chocolate con leche':
                    imagePath = 'assets/leche.webp';
                    break;
                case 'chocolate amargo':
                    imagePath = 'assets/negro.webp';
                    break;
                case 'chocolate blanco':
                    imagePath = 'assets/blanco.webp';
                    break;
                case 'chocolate con nueces':
                    imagePath = 'assets/frutos.webp';
                    break;
            }
            
            if (mainImage && imagePath) {
                mainImage.src = imagePath;
            }
            if (priceDisplay) {
                priceDisplay.textContent = price;
            }
        });
    });

    window.addEventListener('scroll', () => {
        handleNavbarScroll();
        handleParallax();
        handleScrollToTopButton();
    });

    // Estado inicial
    nav.classList.add('bg-[#2B1810]/50', 'py-0');
    logo.style.width = '60px';

    // Animación del navbar al hacer scroll
    window.addEventListener('scroll', function() {
        const nav = document.getElementById('main-nav');
        if (window.scrollY > 50) {
            nav.style.backgroundColor = 'rgba(65, 45, 35, 0.85)';
            nav.style.backdropFilter = 'blur(8px)';
        } else {
            nav.style.backgroundColor = 'rgba(65, 45, 35, 0.7)';
            nav.style.backdropFilter = 'blur(5px)';
        }
    });

    function actualizarPrecio(precio) {
        const precioElement = document.querySelector('#choco-price .precio');
        if (precioElement) {
            precioElement.textContent = precio.toFixed(2);
        }
    }

    function cambiarImagen(imagen) {
        const chocolateImg = document.querySelector('#chocolate-img');
        if (chocolateImg) {
            chocolateImg.src = imagen;
        }
    }

    // Agregar event listeners a cada producto
    productOptions.forEach(option => {
        option.addEventListener('click', function() {
            actualizarProducto(this);
        });
    });
});