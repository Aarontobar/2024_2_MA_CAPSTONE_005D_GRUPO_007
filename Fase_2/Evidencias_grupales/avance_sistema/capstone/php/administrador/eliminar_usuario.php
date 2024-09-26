<?php
if (isset($_GET['id'])) {
    $id_usuario = intval($_GET['id']);

    // Conexión a la base de datos
    $conn = new mysqli("localhost", "root", "", "restaurante_bd");
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Eliminar el usuario
    $sql = "DELETE FROM usuarios WHERE id_usuario = $id_usuario";

    if ($conn->query($sql) === TRUE) {
        // Redirigir a la página de administración de usuarios
        header("Location: admin_usuarios.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>