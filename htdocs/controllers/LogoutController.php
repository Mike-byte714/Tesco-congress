<?php
// Iniciar la sesión para poder cerrarla
session_start();

// Destruir completamente la sesión
session_destroy();

// Redirigir al login de forma ABSOLUTA y SEGURA
// Usamos una redirección forzada con una URL absoluta
header("Location: /views/acceso.php");
exit();
?>