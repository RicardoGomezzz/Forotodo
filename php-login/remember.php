<?php
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
  $_SESSION['user_id'] = $_COOKIE['user_id'];
}

if (isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) !== 'index.php') {
  // Si el usuario ha iniciado sesión y no está en la página de inicio, redirigirlo a la página de inicio
  header('Location: /forotodo/php-login/index.php');
  exit;
}
?>
