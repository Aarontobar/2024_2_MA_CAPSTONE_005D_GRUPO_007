<?php
// Iniciar sesión
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

if (isset($_POST['id_mesa'])) {
    $id_mesa = $_POST['id_mesa'];

    // Consulta para marcar la mesa como disponible
    $query_mesa = "UPDATE Mesa SET estado = 'Disponible' WHERE id_mesa = ?";
    $stmt_mesa = $conn->prepare($query_mesa);
    $stmt_mesa->bind_param("i", $id_mesa);
    
    if ($stmt_mesa->execute()) {
        // Consulta para marcar el detalle del mesero como inactivo
        $query_detalle = "UPDATE detalle_mesero_mesa SET estado = 'inactivo' WHERE id_mesa = ?";
        $stmt_detalle = $conn->prepare($query_detalle);
        $stmt_detalle->bind_param("i", $id_mesa);
        $stmt_detalle->execute();

        echo json_encode(["success" => true, "message" => "La mesa ha sido marcada como limpia y disponible."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar el estado de la mesa."]);
    }

    // Cerrar las declaraciones
    $stmt_mesa->close();
    $stmt_detalle->close();
} else {
    echo json_encode(["success" => false, "message" => "ID de mesa no recibido."]);
}

// Cerrar la conexión
$conn->close();
?>