# Sweet Mett - Tienda de Chocolates Artesanales

Bienvenido a **Sweet Mett**, una tienda web de chocolates artesanales desarrollada como proyecto académico por estudiantes de la Universidad UTEPSA. Este sistema permite a los usuarios explorar, comprar y administrar productos de chocolatería de alta calidad, combinando innovación, tradición y tecnología.

---

## 🚀 Características principales

- **Catálogo de productos**: Visualiza chocolates artesanales con imágenes, precios y descripciones.
- **Galería interactiva**: Sección visual con animaciones y smooth scroll.
- **Carrito de compras**: Añade productos y gestiona tu pedido (solo para usuarios registrados).
- **Sistema de usuarios**: Registro, inicio de sesión y roles (cliente y administrador).
- **Panel de administración**: Gestión de productos, usuarios y pedidos (solo para administradores).
- **Responsive Design**: Interfaz adaptada a dispositivos móviles y escritorio.
- **Animaciones y experiencia de usuario**: Uso de AOS, scroll suave, menú móvil y botones flotantes.
- **Juego interactivo**: Minijuego incluido para mejorar la experiencia del usuario.
- **Contacto rápido**: Botón de WhatsApp y formulario de suscripción a ofertas.

---

## 🛠️ Tecnologías utilizadas

- **Frontend**:  
  - HTML5, CSS3 (Tailwind CSS, estilos personalizados)
  - JavaScript (vanilla, AOS, FontAwesome)
- **Backend**:  
  - PHP 8+
  - MySQL (gestión de usuarios, productos y pedidos)
- **Otros**:  
  - XAMPP (entorno local)
  - Google Fonts

---

## 📁 Estructura del proyecto

```
WEBCHOCOLATECLIENTEADMIN/
│
├── admin/                # Panel de administración
├── assets/               # Imágenes, videos y recursos multimedia
├── cliente/              # Área privada para clientes
├── css/                  # Hojas de estilo (Tailwind, personalizados)
├── includes/             # Archivos PHP reutilizables (sesión, conexión, helpers)
├── js/                   # Scripts JS adicionales
├── config/               # Configuración de base de datos
├── index.php             # Página principal (landing)
├── catalogo.php          # Catálogo de productos
├── login.php             # Inicio de sesión
├── registro.php          # Registro de usuarios
├── logout.php            # Cierre de sesión
├── game.js               # Lógica del minijuego
└── README.md             # Este archivo
```

---

## ⚙️ Instalación y ejecución local

1. **Clona el repositorio:**
   ```bash
   git clone https://github.com/tuusuario/SweetMett.git
   ```

2. **Configura el entorno local:**
   - Instala [XAMPP](https://www.apachefriends.org/) o similar.
   - Copia la carpeta `WEBCHOCOLATECLIENTEADMIN` al directorio `htdocs` de XAMPP.

3. **Configura la base de datos:**
   - Crea una base de datos MySQL (por ejemplo, `sweetmett`).
   - Importa el archivo SQL proporcionado (si existe) o crea las tablas según los scripts en `config/database.php`.

4. **Configura la conexión:**
   - Edita `config/database.php` con tus credenciales de MySQL.

5. **Inicia el servidor:**
   - Abre XAMPP y activa Apache y MySQL.
   - Accede a [http://localhost/WEBCHOCOLATECLIENTEADMIN](http://localhost/WEBCHOCOLATECLIENTEADMIN) en tu navegador.

---

## 👤 Roles de usuario

- **Cliente:** Puede registrarse, iniciar sesión, ver catálogo, agregar al carrito y realizar pedidos.
- **Administrador:** Acceso a panel de administración para gestionar productos, usuarios y pedidos.

---

## 📸 Capturas de pantalla

> Puedes agregar aquí imágenes del home, catálogo, galería, panel admin, etc.

---

## 📄 Créditos

- Proyecto desarrollado por estudiantes de la Universidad UTEPSA, Santa Cruz de la Sierra, Bolivia.
- Inspirado en la pasión por el chocolate y la innovación tecnológica.

---

## 📝 Licencia

Este proyecto es de uso académico y educativo. Puedes modificarlo y adaptarlo según tus necesidades.

---

¡Gracias por visitar Sweet Mett!  
Si te gusta el proyecto, no dudes en darle una estrella ⭐ en GitHub.
