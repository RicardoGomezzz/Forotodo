<?php
session_start();

// Verificar si el usuario es un administrador
if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header('Location: index.php');
    exit;
}

require 'db.php';

// Obtener el nombre de usuario actual de la sesión
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Configuración de paginación
$rowsPerPage = 9;
$totalRows = $conn->query('SELECT count(*) FROM users WHERE admin = 0')->fetchColumn();
$totalPages = ceil($totalRows / $rowsPerPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $rowsPerPage;

// Obtener usuarios para la página actual
$stmt = $conn->prepare('SELECT id, user, email FROM users WHERE admin = 0 ORDER BY id LIMIT :offset, :rowsPerPage');
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':rowsPerPage', $rowsPerPage, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar la eliminación de un usuario
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Verificar que el ID del usuario a eliminar no sea el del administrador actual
    if ($id == $_SESSION['user_id']) {
        header('Location: crud-usuarios.php');
        exit;
    }
    
    // Eliminar el usuario de la base de datos
    $deleteStmt = $conn->prepare('DELETE FROM users WHERE id = :id');
    $deleteStmt->bindParam(':id', $id);
    $deleteStmt->execute();
    
    header('Location: crud-usuarios.php');
    exit;
}

// Procesar la modificación de un usuario
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $user = $_POST['user'];
    $email = $_POST['email'];
    
    // Actualizar los datos del usuario en la base de datos
    $updateStmt = $conn->prepare('UPDATE users SET user = :user, email = :email WHERE id = :id');
    $updateStmt->bindParam(':user', $user);
    $updateStmt->bindParam(':email', $email);
    $updateStmt->bindParam(':id', $id);
    $updateStmt->execute();
    
    header('Location: crud-usuarios.php');
    exit;
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link href="/foroTodo/assets/css/usuarios.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php include 'partials/nav.php'; ?>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Tabla de Usuarios</h2>
                <div class="d-flex justify-content-start" id="btnAgregar">
                    <a href="agregar-usuario.php" class="btn btn-info">Agregar Usuario</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Usuario</th>
                                <th scope="col">Email</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <th scope="row"><?php echo $usuario['id']; ?></th>
                                <td><?php echo $usuario['user']; ?></td>
                                <td><?php echo $usuario['email']; ?></td>
                                <td>
                                    <a href="editar-usuario.php?id=<?php echo $usuario['id']; ?>"
                                        class="btn btn-sm btn-info"><i class="fi fi-rr-pen-square"></i></a>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="openConfirmModal(<?php echo $usuario['id']; ?>)"><i
                                            class="fi fi-rr-delete-user"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Paginación -->
                <nav>
                    <ul class="pagination justify-content-center mt-2">
                        <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo ($currentPage - 1); ?>">Anterior</a>
                        </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($i === $currentPage) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo ($currentPage + 1); ?>">Siguiente</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <!-- Modal de confirmación de eliminación -->
                <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog"
                    aria-labelledby="confirmModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmModalLabel">Confirmar eliminación</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                ¿Estás seguro de eliminar este usuario?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://unpkg.com/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                <script>
                function openConfirmModal(userId) {
                    var confirmModal = document.getElementById('confirmModal');
                    var confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

                    confirmDeleteBtn.addEventListener('click', function() {
                        window.location.href = '?delete=' + userId;
                    });

                    var bootstrapModal = new bootstrap.Modal(confirmModal);
                    bootstrapModal.show();
                }
                </script>
                <script>
                $(document).ready(function() {
                    adjustContainerMargin();

                    $(window).on('resize', function() {
                        adjustContainerMargin();
                    });

                    function adjustContainerMargin() {
                        var windowHeight = $(window).height();
                        var marginSize = windowHeight *
                        0.05; // Ajusta el tamaño del margen según tus necesidades

                        $('#container').css('margin-top', marginSize + 'px');
                    }
                });
                </script>
            </div>
        </div>
    </div>
</body>


</html>