<?php
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id']) && basename($_SERVER['PHP_SELF']) !== 'index.php' && empty($_POST['recordar'])) {
  $_SESSION['user_id'] = $_COOKIE['user_id'];
  header('Location: /forotodo/php-login/index.php');
  exit;
} elseif (isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) === 'index.php' && !empty($_POST['recordar'])) {
  // Si el usuario ha iniciado sesión en la página de inicio y se ha marcado el checkbox "Recordar sesión"
  $cookie_duration = 30 * 24 * 60 * 60; // 30 días en segundos
  setcookie('user_id', $_SESSION['user_id'], time() + $cookie_duration);
} elseif (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id']) && basename($_SERVER['PHP_SELF']) === 'index.php' && empty($_POST['recordar'])) {
  // Si el usuario tiene una cookie pero no ha iniciado sesión en la página de inicio
  setcookie('user_id', '', time() - 3600); // Elimina la cookie
}
?>
