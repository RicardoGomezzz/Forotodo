<?php
session_start();

// Verificar si el usuario es un administrador
if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header('Location: index.php');
    exit;
}

require 'db.php';

$id = $_GET['id'];

// Obtener los datos del usuario de la base de datos
$stmt = $conn->prepare('SELECT id, user, email, nombre, foto FROM users WHERE id = :id');
$stmt->bindParam(':id', $id);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header('Location: crud-usuarios.php');
    exit;
}

$username = $usuario['user'];
$email = $usuario['email'];
$nombre = $usuario['nombre']; 

$foto = $usuario['foto'];

 // Verificar si el usuario tiene una foto asignada
 if (!$foto) {
    // Asignar una foto predeterminada si no tiene foto
    $foto = '../assets/img/perfil2.png'; // Reemplaza 'default.jpg' con el nombre de tu foto predeterminada
  }

// Procesar la actualización del usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $email = $_POST['email'];

    // Verificar si se ingresó una nueva contraseña
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $updateStmt = $conn->prepare('UPDATE users SET user = :user, email = :email, password = :password WHERE id = :id');
        $updateStmt->bindParam(':password', $password);
    } else {
        $updateStmt = $conn->prepare('UPDATE users SET user = :user, email = :email WHERE id = :id');
    }

    $updateStmt->bindParam(':user', $user);
    $updateStmt->bindParam(':email', $email);
    $updateStmt->bindParam(':id', $id);
    $updateStmt->execute();

    header('Location: crud-usuarios.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Modificar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/foroTodo/assets/css/perfil2.css" rel="stylesheet" type="text/css">
</head>

<body style="background-color: dark;">

    <?php include 'partials/nav.php'; ?>

    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row mt-4">
                    <div class="col-md-12">
                        <a class="btn btn-secondary mb-3 crud-btn" href="crud-usuarios.php"><i
                                class="bi bi-arrow-left"></i> Volver al CRUD</a>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="col-md-3 align-self-start custom-position-left">
                        <div class="d-flex flex-column align-items-center text-center p-3 py-5"
                            style="margin-top: 30px;">
                            <div class="profile-title">
                                <h4 class="card-title text-center">Perfil de usuario</h4>
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
                        <h4 class="card-title text-center">Modificar datos del usuario</h4>
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
                                <button class="btn btn-primary" type="submit">Confirmar Edicion</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>

</html>