<?php
session_start();

require '../php-login/remember.php';
require '../php-login/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Obtener los valores ingresados por el usuario
  $usuario = $_POST['usuario'];
  $contenido = $_POST['comentario'];

  // Preparar y ejecutar la consulta SQL
  $stmt = $conn->prepare("INSERT INTO comentarios (publicacion_id, usuario, contenido, fecha_comentario) VALUES (:publicacion_id, :usuario, :contenido, :fecha_comentario)");
  $stmt->bindParam(':publicacion_id', $publicacion_id);
  $stmt->bindParam(':usuario', $usuario);
  $stmt->bindParam(':contenido', $contenido);
  $stmt->bindParam(':fecha_comentario', $fecha_comentario);

  // Establecer los valores de los parámetros
  $publicacion_id = $_POST['publicacion_id'];
  $fecha_comentario = date('Y-m-d H:i:s');

  $stmt->execute();

  // Redireccionar al index o a la página deseada después de agregar el comentario
  header("Location: ../php-login/index.php");
  exit();
}
?>

