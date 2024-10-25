<?php
session_start();

// Establecer la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurante_bd";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deja tu Reseña</title>
    <link rel="stylesheet" href="../../css/reseña.css">
</head>
<body>
    <div class="container">
        <h2>Deja tu reseña</h2>
        <form action="" method="POST" class="review-form">
            <label for="calificacion">Calificación:</label>
            <div id="stars"></div>
            <input type="hidden" id="calificacion" name="calificacion" value="0">

            <label for="nombre_cliente">Nombre:</label>
            <input type="text" id="nombre_cliente" name="nombre_cliente" required>

            <label for="apellido_cliente">Apellido:</label>
            <input type="text" id="apellido_cliente" name="apellido_cliente" required>

            <label for="comentario">Comentario (opcional):</label>
            <textarea id="comentario" name="comentario" rows="4"></textarea>

            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>

    <script src="../../js/reseñas.js"></script> <!-- Enlace al archivo JS -->
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Conexión a la base de datos
    $conexion = new mysqli('localhost', 'root', '', 'restaurante_bd');

    if ($conexion->connect_error) {
        die('Error en la conexión: ' . $conexion->connect_error);
    }

    // Obtener los datos del formulario
    $calificacion = $_POST['calificacion'];
    $nombre_cliente = $conexion->real_escape_string($_POST['nombre_cliente']);
    $apellido_cliente = $conexion->real_escape_string($_POST['apellido_cliente']);
    $comentario = $conexion->real_escape_string($_POST['comentario']);
    $id_pedido = 1; // Aquí debes obtener el id del pedido real

    // Insertar la reseña en la base de datos
    $sql = "INSERT INTO Reseñas (id_pedido, nombre_cliente, apellido_cliente, calificacion, comentario) 
            VALUES ('$id_pedido', '$nombre_cliente', '$apellido_cliente', '$calificacion', '$comentario')";

    if ($conexion->query($sql) === TRUE) {
        // Redirigir al usuario a otra página (aquí puedes definir la página de destino)
        header("Location: tu_pagina.php"); // Cambia 'tu_pagina.php' por la página que deseas redirigir
        exit; // Asegúrate de llamar a exit después de header
    } else {
        echo "<script>alert('Error: " . $sql . "<br>" . $conexion->error . "');</script>";
    }

    $conexion->close();
}
?>