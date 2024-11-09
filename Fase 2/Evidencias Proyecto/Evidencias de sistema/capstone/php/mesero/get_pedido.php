<?php
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id_pedido = $_GET['id_pedido'];

    $sql = "SELECT * FROM pedido WHERE id_pedido = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param('i', $id_pedido);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $pedido = $result->fetch_assoc();
            echo json_encode(['success' => true, 'pedido' => $pedido]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el pedido.']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
    }
}

$conn->close(); // Cerrar la conexión a la base de datos
?>