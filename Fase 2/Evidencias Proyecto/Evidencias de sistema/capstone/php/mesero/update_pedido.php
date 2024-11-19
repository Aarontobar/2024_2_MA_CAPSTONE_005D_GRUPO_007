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
    $estado_pago = $_GET['estado_pago'];
    $estado = $_GET['estado'];
    $estado_mesa = $_GET['estado_mesa'];

    // Consulta para obtener el id_detalle_mesero_mesa asociado al pedido
    $sql_detalle = "SELECT id_detalle_mesero_mesa FROM pedido WHERE id_pedido = ?";
    $stmt_detalle = $conn->prepare($sql_detalle);
    
    if ($stmt_detalle) {
        $stmt_detalle->bind_param('i', $id_pedido);
        $stmt_detalle->execute();
        $result_detalle = $stmt_detalle->get_result();
        
        if ($result_detalle->num_rows > 0) {
            $row = $result_detalle->fetch_assoc();
            $id_detalle_mesero_mesa = $row['id_detalle_mesero_mesa'];

            // Consulta para obtener el id de la mesa desde detalle_mesero_mesa
            $sql_mesa = "SELECT id_mesa FROM detalle_mesero_mesa WHERE id_detalle = ?";
            $stmt_mesa = $conn->prepare($sql_mesa);
            
            if ($stmt_mesa) {
                $stmt_mesa->bind_param('i', $id_detalle_mesero_mesa);
                $stmt_mesa->execute();
                $result_mesa = $stmt_mesa->get_result();
                
                if ($result_mesa->num_rows > 0) {
                    $row_mesa = $result_mesa->fetch_assoc();
                    $id_mesa = $row_mesa['id_mesa'];

                    // Consulta para actualizar el estado del pedido
                    $sql_pedido = "UPDATE pedido SET estado_pago = ?, estado = ? WHERE id_pedido = ?";
                    $stmt_pedido = $conn->prepare($sql_pedido);
                    
                    if ($stmt_pedido) {
                        $stmt_pedido->bind_param('ssi', $estado_pago, $estado, $id_pedido);
                        if ($stmt_pedido->execute()) {
                            // Consulta para actualizar el estado de la mesa
                            $sql_update_mesa = "UPDATE mesa SET estado = ? WHERE id_mesa = ?";
                            $stmt_update_mesa = $conn->prepare($sql_update_mesa);
                            
                            if ($stmt_update_mesa) {
                                $stmt_update_mesa->bind_param('si', $estado_mesa, $id_mesa);
                                if ($stmt_update_mesa->execute()) {
                                    echo json_encode(['success' => true, 'message' => 'El pedido y el estado de la mesa han sido actualizados.']);
                                } else {
                                    echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado de la mesa: ' . $stmt_update_mesa->error]);
                                }
                                $stmt_update_mesa->close();
                            } else {
                                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta para actualizar el estado de la mesa: ' . $conn->error]);
                            }
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado del pedido: ' . $stmt_pedido->error]);
                        }
                        $stmt_pedido->close();
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta para actualizar el estado del pedido: ' . $conn->error]);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se encontró la mesa asociada al detalle del mesero.']);
                }
                $stmt_mesa->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta para obtener la mesa: ' . $conn->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el detalle del mesero asociado al pedido.']);
        }
        $stmt_detalle->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta para obtener el detalle del mesero: ' . $conn->error]);
    }
}

$conn->close(); // Cerrar la conexión a la base de datos
?>