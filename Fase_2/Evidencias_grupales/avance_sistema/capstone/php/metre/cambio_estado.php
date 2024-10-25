<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurante_bd";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode(['error' => 'Conexión fallida: ' . $conn->connect_error]);
    exit();
}

// Obtener la acción y el ID del pedido desde la solicitud
$action = $_POST['action'];
$id_pedido = $_POST['id_pedido'];

if ($action == 'prioritize') {
    // Actualizar la prioridad del pedido a 'prioritario'
    $sql = "UPDATE Pedido SET prioridad = 'prioritario' WHERE id_pedido = ?";
} elseif ($action == 'cancel') {
    // Actualizar el estado del pedido a 'cancelado'
    $sql = "UPDATE Pedido SET estado = 'cancelado' WHERE id_pedido = ?";
} else {
    echo json_encode(['error' => 'Acción no válida']);
    exit();
}

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pedido);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Error al actualizar el pedido: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>