<?php
session_start();

require 'db.php';

$username = null;
$email = null;
$nombre = null;

if (isset($_SESSION['user_id'])) {
  $records = $conn->prepare('SELECT id, user, email, nombre, foto FROM users WHERE id = :id');
  $records->bindParam(':id', $_SESSION['user_id']);
  $records->execute();
  $results = $records->fetch(PDO::FETCH_ASSOC);

  if (count($results) > 0) {
    $username = $results['user'];
    $email = $results['email'];
    $nombre = $results['nombre'];
    $foto = $results['foto'];

    // Verificar si el usuario tiene una foto asignada
    if (!$foto) {
      // Asignar una foto predeterminada si no tiene foto
      $foto = '../assets/img/perfil2.png'; // Reemplaza 'default.jpg' con el nombre de tu foto predeterminada
    }
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newNombre = $_POST['nombre'];
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $newPassword = $_POST['password'];

    // Verificar si se ingresó un nuevo valor en el campo de contraseña
    if (!empty($newPassword)) {
      $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    } else {
      // Si no se ingresó un nuevo valor en el campo de contraseña, obtener la contraseña existente
      $existingPasswordQuery = $conn->prepare('SELECT password FROM users WHERE id = :id');
      $existingPasswordQuery->bindParam(':id', $_SESSION['user_id']);
      $existingPasswordQuery->execute();
      $existingPasswordResult = $existingPasswordQuery->fetch(PDO::FETCH_ASSOC);
      $hashedPassword = $existingPasswordResult['password'];
    }

    $updateQuery = $conn->prepare('UPDATE users SET nombre = :nombre, user = :username, email = :email, password = :password WHERE id = :id');
    $updateQuery->bindParam(':nombre', $newNombre);
    $updateQuery->bindParam(':username', $newUsername);
    $updateQuery->bindParam(':email', $newEmail);
    $updateQuery->bindParam(':password', $hashedPassword);
    $updateQuery->bindParam(':id', $_SESSION['user_id']);

    if ($updateQuery->execute()) {
      // Actualización exitosa, redirigir a la página de perfil o mostrar un mensaje de éxito
      header('Location: perfil.php');
      exit;
    } else {
      // Error al actualizar, mostrar un mensaje de error
      echo 'Error al actualizar los datos';
    }
  }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/foroTodo/assets/css/perfil.css" rel="stylesheet" type="text/css">
    <style>
        .custom-container {
            margin-top: 30px;
        }

        @media (max-width: 767px) {
            .custom-position-left {
                order: 2;
            }

            .custom-position-right {
                order: 1;
                margin-top: 20px;
            }
        }
    </style>
</head>

<body>

    <?php include 'partials/nav.php'; ?>

    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row mt-4">
                    <div class="col-md-3 align-self-start custom-position-left">
                        <div class="d-flex flex-column align-items-center text-center p-3 py-5"
                            style="margin-top: 30px;">
                            <div class="profile-title">
                                <h4 class="card-title text-center">Mi Perfil</h4>
                            </div>
                            <img class="rounded-circle mt-5" width="150px" src="/forotodo/Image/<?php echo $foto; ?>">
                            <span class="font-weight-bold mt-4"><?php echo $username; ?></span>
                            <span class="text-black-50"><?php echo $email; ?></span>
                            <form method="POST" action="proce_img.php" enctype="multipart/form-data">
                                <input type="file" name="photo" accept="image/*" style="display: none;" id="fileInput">
                                <label for="fileInput" class="btn btn-primary mt-3">Editar Foto</label>
                                <button class="btn btn-primary mt-3" type="submit" name="submit"
                                    style="display: none;">Guardar Foto</button>
                            </form>
                            <?php if ($foto !== '../assets/img/perfil2.png') : ?>
                            <form method="POST" action="proce_img.php">
                                <input type="hidden" name="delete_photo" value="true">
                                <button class="btn btn-outline-danger" type="submit">Eliminar Foto</button>
                            </form>
                            <?php endif; ?>

                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 justify-content-end custom-position-right">
                        <h4 class="card-title text-center">Modifica tus datos</h4>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" class="form-control" placeholder="<?php echo $nombre; ?>"
                                            value="<?php echo $nombre; ?>" name="nombre" id="nombre">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre de Usuario</label>
                                        <input type="text" class="form-control" placeholder="<?php echo $username; ?>"
                                            value="<?php echo $username; ?>" name="username" id="username">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" placeholder="<?php echo $email; ?>"
                                            value="<?php echo $email; ?>" name="email" id="email">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" placeholder="Contraseña" value=""
                                            name="password" id="password">
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-primary" type="submit">Guardar Perfil</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('fileInput').addEventListener('change', function() {
        document.querySelector('button[name="submit"]').style.display = 'block';
    });
    </script>

</body>

</html>
