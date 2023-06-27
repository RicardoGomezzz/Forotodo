<?php
session_start();

require '../php-login/db.php';

$username = null;
$email = null;
$nombre = null;

if (isset($_SESSION['user_id'])) {
  $records = $conn->prepare('SELECT id, user, email, nombre FROM users WHERE id = :id');
  $records->bindParam(':id', $_SESSION['user_id']);
  $records->execute();
  $results = $records->fetch(PDO::FETCH_ASSOC);

  if (count($results) > 0) {
    $username = $results['user'];
    $email = $results['email'];
    $nombre = $results['nombre'];
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newNombre = $_POST['nombre'];
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $newPassword = $_POST['password'];
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

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
</head>

<body>

    <?php include 'partials/nav.php'; ?>

    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row mt-4">
                    <div class="col-md-12">
                    </div>
                    <div class="col-md-3 align-self-start custom-position-left">
                        <div class="d-flex flex-column align-items-center text-center p-3 py-5" style="margin-top: 30px;">
                            <div class="profile-title">
                                <h4 class="card-title text-center">Mi Perfil</h4>
                            </div>
                            <img class="rounded-circle mt-5" width="150px"
                                src="/forotodo/assets/img/perfil2.png">
                            <span class="font-weight-bold"><?php echo $username; ?></span>
                            <span class="text-black-50"><?php echo $email; ?></span>
                            <span></span>
                            <button class="btn btn-primary mt-3" id="editPhotoBtn">Editar Foto</button>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 justify-content-end custom-position-right">
                        <h4 class="card-title text-center">Modifica tus datos</h4>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" class="form-control" placeholder="<?php echo $nombre; ?>" value="<?php echo $nombre; ?>" name="nombre" id="nombre">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre de Usuario</label>
                                        <input type="text" class="form-control" placeholder="<?php echo $username; ?>" value="<?php echo $username; ?>" name="username" id="username">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" placeholder="<?php echo $email; ?>" value="<?php echo $email; ?>" name="email" id="email">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" placeholder="Contraseña" value="" name="password" id="password">
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

    <!-- JavaScript para el botón de editar foto -->
    <script>
    document.getElementById("editPhotoBtn").addEventListener("click", function() {
        // Lógica para editar la foto de perfil
        alert("Editar foto de perfil");
    });
    </script>

</body>

</html>
