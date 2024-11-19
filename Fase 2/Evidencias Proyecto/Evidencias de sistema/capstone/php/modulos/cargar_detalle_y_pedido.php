<?php
header('Content-Type: application/json');

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurante_bd";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    error_log("Conexión fallida: " . $conn->connect_error);
    die(json_encode(['success' => false, 'message' => "Conexión fallida: " . $conn->connect_error]));
}

// Obtener el table_id de la solicitud
$table_id = isset($_GET['table_id']) ? intval($_GET['table_id']) : 0;
error_log("table_id recibido: " . $table_id);

if ($table_id <= 0) {
    error_log("Invalid table_id: " . $table_id);
    echo json_encode(['error' => 'Invalid table_id']);
    $conn->close();
    exit();
}

// Consulta para obtener el detalle mesero mesa activo para la mesa específica
$sql = "SELECT * FROM detalle_mesero_mesa WHERE id_mesa = ? AND estado = 'activo'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $table_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Convertir los resultados a un array asociativo
    $detalle_mesero_mesa = $result->fetch_assoc();
    error_log("Detalle mesero mesa encontrado: " . json_encode($detalle_mesero_mesa));

    // Obtener el id_detalle del detalle mesero mesa
    $id_detalle_mesero_mesa = $detalle_mesero_mesa['id_detalle'];
    $id_usuario = $detalle_mesero_mesa['id_usuario'];
    error_log("id_detalle_mesero_mesa: " . $id_detalle_mesero_mesa);
    error_log("id_usuario: " . $id_usuario);

    // Consulta para obtener el pedido relacionado con el id_detalle_mesero_mesa
    $sql = "SELECT * FROM Pedido WHERE id_detalle_mesero_mesa = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_detalle_mesero_mesa);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Convertir los resultados a un array asociativo
        $pedido = $result->fetch_assoc();
        error_log("Pedido encontrado: " . json_encode($pedido));
        echo json_encode(['pedido' => $pedido, 'id_detalle_mesero_mesa' => $id_detalle_mesero_mesa, 'id_usuario' => $id_usuario]);
    } else {
        error_log("No order found for id_detalle_mesero_mesa: " . $id_detalle_mesero_mesa);
        echo json_encode(['pedido' => null, 'id_detalle_mesero_mesa' => $id_detalle_mesero_mesa, 'id_usuario' => $id_usuario]);
    }
} else {
    error_log("No active waiter-table detail found for table_id: " . $table_id);
    echo json_encode(['error' => 'No active waiter-table detail found']);
}

$stmt->close();
$conn->close();
?>