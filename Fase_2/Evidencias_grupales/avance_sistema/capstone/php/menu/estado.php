<?php
header('Content-Type: application/json');

// Establecer la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurante_bd";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(['error' => 'Conexión fallida: ' . $conn->connect_error]));
}

// Obtener el ID del pedido de la URL
$id_pedido = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_pedido <= 0) {
    die(json_encode(['error' => 'ID de pedido no válido.']));
}

// Consultar el estado del pedido
$sql = "SELECT estado FROM Pedido WHERE id_pedido = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_pedido);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$stmt->close();
$conn->close();

// Enviar la respuesta en formato JSON
if ($data) {
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Pedido no encontrado.']);
}
?>