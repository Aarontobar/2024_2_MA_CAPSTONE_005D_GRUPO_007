<?php
session_start();

// Establecer conexión a la base de datos
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

// Obtener el ID de la mesa desde la URL
$id_mesa = isset($_GET['mesa_id']) ? intval($_GET['mesa_id']) : 0;

// Consultar si hay un detalle mesero mesa para la mesa seleccionada
$sql = "SELECT id_usuario FROM detalle_mesero_mesa WHERE id_mesa = ? AND estado = 'activo'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_mesa);
$stmt->execute();
$result = $stmt->get_result();

$response = ['id_usuario' => null, 'qr_url' => '', 'link_menu' => ''];

if ($result->num_rows > 0) {
    $detalle = $result->fetch_assoc();
    $response['id_usuario'] = $detalle['id_usuario'];

    // Generar el enlace al menú
    $link_menu = "../menu/ver_menu.php?mesa_id=" . urlencode($id_mesa) . "&usuario_id=" . urlencode($response['id_usuario']);
    $response['link_menu'] = $link_menu;

    // Generar la URL del QR
    $qr_api_url = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($link_menu) . "&size=200x200";
    $response['qr_url'] = $qr_api_url;
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);