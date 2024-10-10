<?php
include 'conexion.php'; // Asegúrate de incluir tu conexión a la base de datos

$id_destinatario = $_POST['id_destinatario'];
$mensaje = $_POST['mensaje'];
$id_usuario_envia = $_POST['id_usuario_envia']; // Obtener el ID del usuario que envía

// Preparar la consulta para insertar el mensaje
$stmt = $conn->prepare("INSERT INTO mensajes (id_usuario_envia, id_usuario_recibe, mensaje) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $id_usuario_envia, $id_destinatario, $mensaje);

try {
    $stmt->execute();
    echo json_encode(['status' => 'success']);
} catch (mysqli_sql_exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>