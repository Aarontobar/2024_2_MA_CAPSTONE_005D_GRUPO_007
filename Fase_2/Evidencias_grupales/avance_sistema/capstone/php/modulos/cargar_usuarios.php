<?php
include '../modulos/conexion.php';

$id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;

// Recuperar todos los usuarios
$sql = "SELECT u.id_usuario, u.nombre_usuario FROM usuarios u WHERE u.id_usuario != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuarios = $result->fetch_all(MYSQLI_ASSOC);

// Devolver la lista de usuarios como JSON
header('Content-Type: application/json');
echo json_encode($usuarios);