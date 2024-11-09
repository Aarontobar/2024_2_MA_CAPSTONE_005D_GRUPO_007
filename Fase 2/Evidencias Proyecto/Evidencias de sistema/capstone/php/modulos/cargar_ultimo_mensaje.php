<?php
include 'conexion.php';

$id_usuario = $_GET['id_usuario'];
$id_destinatario = $_GET['id_destinatario'];

$query = "SELECT * FROM mensajes WHERE (id_usuario_envia = $id_usuario AND id_usuario_recibe = $id_destinatario) OR (id_usuario_envia = $id_destinatario AND id_usuario_recibe = $id_usuario) ORDER BY fecha_hora DESC LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result) {
    $message = mysqli_fetch_assoc($result);
    echo json_encode($message);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'No se encontró el último mensaje']);
}

mysqli_close($conn);
?>