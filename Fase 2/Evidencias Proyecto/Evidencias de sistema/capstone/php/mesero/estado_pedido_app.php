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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pedido = $_POST['id_pedido'];

    // Definir los estados en orden
    $estados = ['recibido', 'en preparación', 'preparado', 'servido', 'completado', 'cancelado'];

    // 1. Consulta para obtener el estado actual del pedido
    $sql = "SELECT estado FROM pedido WHERE id_pedido = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param('i', $id_pedido);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // 2. Obtener el estado actual
            $row = $result->fetch_assoc();
            $estado_actual = $row['estado'];

            // 3. Encontrar el índice del estado actual
            $indice_actual = array_search($estado_actual, $estados);

            // 4. Verificar si hay un siguiente estado
            if ($indice_actual !== false && $indice_actual < count($estados) - 1) {
                $nuevo_estado = $estados[$indice_actual + 1]; // Asignar el siguiente estado

                // 5. Preparar la consulta para actualizar el estado del pedido
                $update_sql = "UPDATE pedido SET estado = ? WHERE id_pedido = ?";
                $update_stmt = $conn->prepare($update_sql);
                
                if ($update_stmt) {
                    $update_stmt->bind_param('si', $nuevo_estado, $id_pedido);
                    if ($update_stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => "Estado del pedido actualizado a '$nuevo_estado' correctamente."]);
                    } else {
                        echo json_encode(['success' => false, 'message' => "Error al actualizar el estado del pedido: " . $update_stmt->error]);
                    }
                    $update_stmt->close();
                } else {
                    echo json_encode(['success' => false, 'message' => "Error al preparar la consulta de actualización: " . $conn->error]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => "No se puede avanzar más en el estado del pedido."]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => "No se encontró el pedido con ID: $id_pedido."]);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => "Error al preparar la consulta: " . $conn->error]);
    }
}

$conn->close(); // Cerrar la conexión a la base de datos
?>