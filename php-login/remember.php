<?php
// Verificar si la cookie "user_id" existe y no hay una sesión activa
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    // Obtener el ID de usuario de la cookie
    $user_id = $_COOKIE['user_id'];

    // Buscar al usuario en la base de datos por su ID
    $query = "SELECT id FROM users WHERE id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Iniciar la sesión del usuario
        $_SESSION['user_id'] = $user['id'];

        // Renovar la cookie de "Recuerda mi sesión" para extender su duración
        $cookie_duration = 30 * 24 * 60 * 60; // 30 días en segundos
        setcookie('user_id', $user['id'], time() + $cookie_duration);
    } else {
        // Si el usuario no existe, eliminar la cookie
        setcookie('user_id', '', time() - 3600);
    }
}
?>
