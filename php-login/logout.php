<?php
session_start();

// Eliminar la sesión actual
session_unset();
session_destroy();

// Eliminar la cookie "user_id" si existe
if (isset($_COOKIE['user_id'])) {
  setcookie('user_id', '', time() - 3600);
}

header('Location: /forotodo/php-login/login.php');
?>
