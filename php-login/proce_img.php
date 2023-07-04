<?php
session_start();
require 'db.php';

if (isset($_SESSION['user_id'])) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Obtener la información del archivo enviado
    $file = $_FILES['photo'];
    $fileName = $file['name'];
    $fileTmpPath = $file['tmp_name'];
    $fileType = $file['type'];
    $fileError = $file['error'];

    // Validar si se seleccionó un archivo correctamente
    if ($fileError === UPLOAD_ERR_OK) {
      // Obtener la extensión del archivo
      $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

      // Directorio de destino para almacenar la imagen
      $uploadDir = 'C:\xampp\htdocs\ForoTodo\Image\\';

      // Generar un nombre único para el archivo
      $newFileName = uniqid() . '.' . $fileExtension;

      // Verificar si la foto ya existe en la base de datos
      $existingPhotoQuery = $conn->prepare('SELECT foto FROM users WHERE id = :id');
      $existingPhotoQuery->bindParam(':id', $_SESSION['user_id']);
      $existingPhotoQuery->execute();
      $existingPhotoResult = $existingPhotoQuery->fetch(PDO::FETCH_ASSOC);
      $existingPhoto = $existingPhotoResult['foto'];

      if ($existingPhoto && $existingPhoto !== '../assets/img/perfil2.png') {
        // Eliminar la foto anterior si existe y es diferente a la nueva
        unlink($uploadDir . $existingPhoto);
      }

      if (move_uploaded_file($fileTmpPath, $uploadDir . $newFileName)) {
        // Actualizar la columna de foto en la tabla users
        $updateQuery = $conn->prepare('UPDATE users SET foto = :foto WHERE id = :id');
        $updateQuery->bindParam(':foto', $newFileName);
        $updateQuery->bindParam(':id', $_SESSION['user_id']);

        if ($updateQuery->execute()) {
          // Actualización exitosa, guardar la foto asignada en la variable de sesión
          $_SESSION['user_foto'] = $newFileName;

          // Redirigir a la página de perfil o mostrar un mensaje de éxito
          header('Location: perfil.php');
          exit;
        } else {
          // Error al actualizar, mostrar un mensaje de error
          echo 'Error al actualizar la foto de perfil';
        }
      } else {
        // Error al mover el archivo, mostrar un mensaje de error
        echo 'Error al subir la foto de perfil';
      }
    } else {
      // Error al cargar el archivo, mostrar un mensaje de error
      echo 'Error al seleccionar la foto de perfil';
    }
  } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_photo'])) {
    // Verificar si la foto actual es diferente a la foto predeterminada
    $currentPhotoQuery = $conn->prepare('SELECT foto FROM users WHERE id = :id');
    $currentPhotoQuery->bindValue(':id', $_SESSION['user_id']);
    $currentPhotoQuery->execute();
    $currentPhotoResult = $currentPhotoQuery->fetch(PDO::FETCH_ASSOC);
    $currentPhoto = $currentPhotoResult['foto'];

    if ($currentPhoto !== '../assets/img/perfil2.png') {
      // Eliminar la foto actual
      $uploadDir = 'C:\xampp\htdocs\ForoTodo\Image\\';
      unlink($uploadDir . $currentPhoto);

      // Actualizar la columna de foto en la tabla users
      $updateQuery = $conn->prepare('UPDATE users SET foto = :foto WHERE id = :id');
      $updateQuery->bindValue(':foto', '../assets/img/perfil2.png');
      $updateQuery->bindValue(':id', $_SESSION['user_id']);

      if ($updateQuery->execute()) {
        // Eliminación exitosa, eliminar la foto asignada de la variable de sesión
        unset($_SESSION['user_foto']);

        // Redirigir a la página de perfil o mostrar un mensaje de éxito
        header('Location: perfil.php');
        exit;
      } else {
        // Error al eliminar, mostrar un mensaje de error
        echo 'Error al eliminar la foto de perfil';
      }
    }
  }
}
?>
