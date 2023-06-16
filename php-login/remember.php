<?php
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
  $_SESSION['user_id'] = $_COOKIE['user_id'];
}

if (isset($_SESSION['user_id'])) {
  // Si el usuario ha iniciado sesión, no es necesario redirigirlo
  return;
}
