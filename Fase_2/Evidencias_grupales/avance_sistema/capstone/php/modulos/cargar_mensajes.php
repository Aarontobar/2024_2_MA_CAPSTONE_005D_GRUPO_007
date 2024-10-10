<?php
include '../modulos/conexion.php';

$id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;
$id_destinatario = isset($_GET['id_destinatario']) ? intval($_GET['id_destinatario']) : 0;

$sql = "SELECT m.*, u1.nombre_usuario AS usuario_envia 
        FROM mensajes m
        JOIN usuarios u1 ON m.id_usuario_envia = u1.id_usuario
        WHERE (m.id_usuario_envia = ? AND m.id_usuario_recibe = ?) 
        OR (m.id_usuario_envia = ? AND m.id_usuario_recibe = ?)
        ORDER BY m.fecha_hora ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $id_usuario, $id_destinatario, $id_destinatario, $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$mensajes = $result->fetch_all(MYSQLI_ASSOC);

// Devolver los mensajes como JSON
header('Content-Type: application/json');
echo json_encode($mensajes);