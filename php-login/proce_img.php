<?php
session_start();

require '../php-login/db.php';

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

      // Mover el archivo al directorio de destino
      $uploadPath = $uploadDir . $newFileName;
      
      // Verificar si la foto ya existe en la base de datos
      $existingPhotoQuery = $conn->prepare('SELECT foto FROM users WHERE id = :id');
      $existingPhotoQuery->bindParam(':id', $_SESSION['user_id']);
      $existingPhotoQuery->execute();
      $existingPhotoResult = $existingPhotoQuery->fetch(PDO::FETCH_ASSOC);
      $existingPhoto = $existingPhotoResult['foto'];
      
      if ($existingPhoto && $existingPhoto !== $newFileName) {
        // Eliminar la foto anterior si existe y es diferente a la nueva
        unlink($uploadDir . $existingPhoto);
      }

      if (move_uploaded_file($fileTmpPath, $uploadPath)) {
        // Actualizar la columna de foto en la tabla users
        $updateQuery = $conn->prepare('UPDATE users SET foto = :foto WHERE id = :id');
        $updateQuery->bindParam(':foto', $newFileName);
        $updateQuery->bindParam(':id', $_SESSION['user_id']);

        if ($updateQuery->execute()) {
          // Actualización exitosa, redirigir a la página de perfil o mostrar un mensaje de éxito
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
  }
}
?>
