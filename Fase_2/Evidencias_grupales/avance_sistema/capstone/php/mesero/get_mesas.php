<?php
// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurante_bd";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Obtener el id del mesero desde el enlace
$id_usuario = isset($_GET['id_mesero']) ? intval($_GET['id_mesero']) : 0;
if ($id_usuario <= 0) {
    die(json_encode(["error" => "ID de usuario inválido"]));
}

// Consultar las mesas asignadas al mesero (detalle_mesero_mesa y mesas)
$sql_mesas = "SELECT M.id_mesa, M.cantidad_asientos, M.estado 
              FROM detalle_mesero_mesa DMM
              JOIN mesa M ON DMM.id_mesa = M.id_mesa
              WHERE DMM.id_usuario = ? AND DMM.estado = 'activo'";

$stmt_mesas = $conn->prepare($sql_mesas);
$stmt_mesas->bind_param("i", $id_usuario);
$stmt_mesas->execute();
$result_mesas = $stmt_mesas->get_result();

// Recoger las mesas asignadas
$mesas = [];
while ($row = $result_mesas->fetch_assoc()) {
    $mesas[] = $row;
}

// Cerrar la consulta de mesas
$stmt_mesas->close();

// Consultar si las mesas tienen pedidos activos hoy
$pedidos_hoy = [];
if (!empty($mesas)) {
    // Crear un arreglo con los IDs de las mesas
    $mesa_ids = array_column($mesas, 'id_mesa');
    $placeholders = implode(',', array_fill(0, count($mesa_ids), '?'));

    // Consulta para obtener los pedidos activos hoy para las mesas asignadas
    $sql_pedidos_hoy = "SELECT P.id_pedido, P.id_detalle_mesero_mesa, P.total_cuenta, P.hora, P.fecha, DMM.id_mesa
                        FROM Pedido P
                        JOIN detalle_mesero_mesa DMM ON P.id_detalle_mesero_mesa = DMM.id_detalle
                        WHERE DMM.id_mesa IN ($placeholders)
                        AND P.fecha = CURDATE()";  // Solo pedidos del día de hoy

    // Preparar la consulta de pedidos
    $stmt_pedidos_hoy = $conn->prepare($sql_pedidos_hoy);

    // Vincular los parámetros dinámicos para las mesas
    $types = str_repeat('i', count($mesa_ids));
    $stmt_pedidos_hoy->bind_param($types, ...$mesa_ids);
    $stmt_pedidos_hoy->execute();
    $result_pedidos_hoy = $stmt_pedidos_hoy->get_result();

    // Recoger los pedidos activos hoy
    while ($row = $result_pedidos_hoy->fetch_assoc()) {
        $pedidos_hoy[$row['id_mesa']] = $row; // Asociar pedidos por id_mesa
    }

    // Cerrar la consulta de pedidos
    $stmt_pedidos_hoy->close();
}

// Cerrar la conexión
$conn->close();

// Retornar los datos en formato JSON
echo json_encode([
    "mesas" => $mesas,
    "pedidos" => $pedidos_hoy
]);
?>