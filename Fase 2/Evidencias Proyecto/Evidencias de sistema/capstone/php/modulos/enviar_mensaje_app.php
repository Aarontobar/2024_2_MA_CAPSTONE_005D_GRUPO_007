<?php
include 'conexion.php'; // Asegúrate de incluir tu conexión a la base de datos

// Verificar que los datos se están recibiendo correctamente
error_log("POST data: " . print_r($_POST, true));

$id_destinatario = isset($_POST['id_destinatario']) ? $_POST['id_destinatario'] : null;
$mensaje = isset($_POST['mensaje']) ? $_POST['mensaje'] : null;
$id_usuario_envia = isset($_POST['id_usuario_envia']) ? $_POST['id_usuario_envia'] : null;

if ($id_destinatario === null || $mensaje === null || $id_usuario_envia === null) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

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