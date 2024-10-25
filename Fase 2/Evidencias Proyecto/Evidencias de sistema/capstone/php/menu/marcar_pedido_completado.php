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

// Obtener el ID del pedido de la solicitud POST
$id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;

// Verificar que se ha recibido un ID de pedido
if ($id_pedido > 0) {
    // Consultar el mesa_id desde el detalle del pedido
    $sql_mesa = "SELECT dm.id_mesa FROM Pedido p join detalle_mesero_mesa dm on p.id_detalle_mesero_mesa = dm.id_detalle WHERE p.id_pedido = ?";
    $stmt_mesa = $conn->prepare($sql_mesa);
    $stmt_mesa->bind_param("i", $id_pedido);
    $stmt_mesa->execute();
    $result_mesa = $stmt_mesa->get_result();

    if ($result_mesa->num_rows > 0) {
        $row = $result_mesa->fetch_assoc();
        $mesa_id = $row['id_mesa'];

        // Marcar el pedido como completado
        $sql_update = "UPDATE Pedido SET estado = 'completado' WHERE id_pedido = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $id_pedido);

        if ($stmt_update->execute()) {
            // Si se marcó correctamente, redirigir a la página de QR
            header("Location: ../cliente/reseña.php");
            exit();
        } else {
            echo "Error al marcar el pedido como completado: " . $stmt_update->error;
        }

        $stmt_update->close();
    } else {
        echo "No se encontró la mesa asociada con el pedido.";
    }

    $stmt_mesa->close();
} else {
    echo "ID de pedido no válido.";
}

// Cerrar la conexión
$conn->close();
?>