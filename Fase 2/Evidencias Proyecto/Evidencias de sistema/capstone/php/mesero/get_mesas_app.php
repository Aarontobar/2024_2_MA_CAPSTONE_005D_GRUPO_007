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
$id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;
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
    $sql_pedidos_hoy = "SELECT P.id_pedido, P.id_detalle_mesero_mesa, P.total_cuenta, P.hora, P.fecha, p.estado, DMM.id_mesa
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

// Generar notificaciones
$notificaciones = [];
foreach ($mesas as $mesa) {
    $estado_mesa = $mesa['estado'];
    $pedido_activo = isset($pedidos_hoy[$mesa['id_mesa']]) ? $pedidos_hoy[$mesa['id_mesa']] : null;

    if ($estado_mesa === 'Disponible') {
        $notificaciones[] = "Mesa ID {$mesa['id_mesa']} está disponible.";
    } elseif ($estado_mesa === 'Ocupada' && !$pedido_activo) {
        $notificaciones[] = "Mesa ID {$mesa['id_mesa']} está ocupada. Debe tomar el pedido.";
    } elseif ($estado_mesa === 'Reservada') {
        $notificaciones[] = "Mesa ID {$mesa['id_mesa']} está reservada.";
    } elseif ($estado_mesa === 'En Espera') {
        $notificaciones[] = "Mesa ID {$mesa['id_mesa']} está en espera. Debe tomar el pedido.";
    } elseif ($estado_mesa === 'Para Limpiar') {
        $notificaciones[] = "Mesa ID {$mesa['id_mesa']} necesita limpieza.";
    }

    if ($pedido_activo) {
        if ($pedido_activo['estado'] === 'preparado') {
            $notificaciones[] = "Pedido ID {$pedido_activo['id_pedido']} está listo para ser llevado.";
        } elseif ($pedido_activo['estado'] === 'en preparación') {
            $notificaciones[] = "Pedido ID {$pedido_activo['id_pedido']} está en preparación.";
        } elseif ($pedido_activo['estado'] === 'recibido') {
            $notificaciones[] = "Pedido ID {$pedido_activo['id_pedido']} ha sido recibido.";
        } elseif ($pedido_activo['estado'] === 'servido') {
            $notificaciones[] = "Pedido ID {$pedido_activo['id_pedido']} ha sido servido.";
        } elseif ($pedido_activo['estado'] === 'completado') {
            $notificaciones[] = "Pedido ID {$pedido_activo['id_pedido']} ha sido completado.";
        } elseif ($pedido_activo['estado'] === 'cancelado') {
            $notificaciones[] = "Pedido ID {$pedido_activo['id_pedido']} ha sido cancelado.";
        }
    }
}

// Cerrar la conexión
$conn->close();

// Retornar los datos en formato JSON
echo json_encode([
    "mesas" => $mesas,
    "pedidos" => $pedidos_hoy,
    "notificaciones" => $notificaciones
]);
?>