<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']);
}

// Función para verificar si el usuario es administrador
function isAdmin() {
    return isLoggedIn() && $_SESSION['rol'] === 'admin';
}

// Función para verificar si el usuario es cliente
function isClient() {
    return isLoggedIn() && $_SESSION['rol'] === 'cliente';
}

// Función para obtener el nombre del usuario actual
function getCurrentUserName() {
    return $_SESSION['nombre'] ?? null;
}

// Función para obtener el ID del usuario actual
function getCurrentUserId() {
    return $_SESSION['usuario'] ?? null;
}

// Función para obtener el rol del usuario actual
function getCurrentUserRole() {
    return $_SESSION['rol'] ?? null;
}

// Función para verificar acceso a rutas protegidas
function checkAccessRights() {
    $currentPath = $_SERVER['PHP_SELF'];
    $basePath = '/sweetmett'; // Ajusta esto según tu configuración
    
    // Si no está logueado y no está en login o registro
    if (!isLoggedIn() && 
        !in_array(basename($currentPath), ['login.php', 'registro.php', 'index.php'])) {
        header('Location: ' . $basePath . '/login.php');
        exit();
    }
    
    // Si está en área de admin sin ser admin
    if (strpos($currentPath, 'admin/') !== false && !isAdmin()) {
        header('Location: ' . $basePath . '/cliente/mi_cuenta.php');
        exit();
    }
    
    // Si está en área de cliente sin ser cliente
    if (strpos($currentPath, 'cliente/') !== false && !isClient()) {
        if (isAdmin()) {
            header('Location: ' . $basePath . '/admin/dashboard.php');
        } else {
            header('Location: ' . $basePath . '/login.php');
        }
        exit();
    }
    
    // Si está logueado y trata de acceder a login o registro
    if (isLoggedIn() && 
        in_array(basename($currentPath), ['login.php', 'registro.php'])) {
        redirectBasedOnRole();
    }
}

// Función para redirigir según el rol
function redirectBasedOnRole() {
    $basePath = '/sweetmett'; // Ajusta esto según tu configuración
    
    if (!isLoggedIn()) {
        header('Location: ' . $basePath . '/login.php');
        exit();
    }

    if (isAdmin()) {
        header('Location: ' . $basePath . '/admin/dashboard.php');
        exit();
    }

    if (isClient()) {
        header('Location: ' . $basePath . '/cliente/mi_cuenta.php');
        exit();
    }
}

// Función para sanitizar datos
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para generar un token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Función para verificar el token CSRF
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

// Función para formatear fechas
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

// Función para formatear moneda
function formatMoney($amount) {
    return number_format($amount, 2, '.', ',');
}

// Función para generar un slug único
function generateSlug($text) {
    // Convertir a minúsculas
    $text = strtolower($text);
    
    // Reemplazar caracteres especiales
    $text = str_replace(
        array('á', 'é', 'í', 'ó', 'ú', 'ñ', ' '),
        array('a', 'e', 'i', 'o', 'u', 'n', '-'),
        $text
    );
    
    // Eliminar caracteres que no sean alfanuméricos o guiones
    $text = preg_replace('/[^a-z0-9\-]/', '', $text);
    
    // Eliminar guiones múltiples
    $text = preg_replace('/-+/', '-', $text);
    
    // Eliminar guiones al inicio y final
    $text = trim($text, '-');
    
    return $text;
}

// Función para validar email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Función para validar contraseña
function validatePassword($password) {
    // Mínimo 8 caracteres, al menos una letra y un número
    return strlen($password) >= 8 && 
           preg_match('/[A-Za-z]/', $password) && 
           preg_match('/[0-9]/', $password);
}

// Función para generar un mensaje de error o éxito
function setMessage($type, $message) {
    $_SESSION['message'] = [
        'type' => $type,
        'text' => $message
    ];
}

// Función para obtener y limpiar el mensaje
function getMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        return $message;
    }
    return null;
}

// Llamar a checkAccessRights al inicio de cada página
checkAccessRights();

$sql = "SELECT p.*, u.nombre 
        FROM pedidos p 
        JOIN usuarios u ON p.id_usuario = u.id";
?>