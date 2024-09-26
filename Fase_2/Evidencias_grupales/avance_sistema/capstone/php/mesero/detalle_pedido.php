<?php
// Conectar a la base de datos
$servername = "localhost"; // Cambiar si es necesario
$username = "root"; // Cambiar si es necesario
$password = ""; // Cambiar si es necesario
$dbname = "restaurante_bd";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener ID de la mesa desde la solicitud
$id_mesa = isset($_GET['id_mesa']) ? intval($_GET['id_mesa']) : 0;

// Consultar detalles del pedido
$sql = "SELECT p.id_pedido, p.total_cuenta, p.hora, p.fecha, p.estado, GROUP_CONCAT(CONCAT(d.id_platillo, ' (', d.cantidad, ')') SEPARATOR ', ') AS platillos
        FROM Pedido p
        JOIN Detalle_Pedido_Platillo d ON p.id_pedido = d.id_pedido
        WHERE p.id_mesa = ?
        GROUP BY p.id_pedido";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_mesa);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo '<h3>Detalles del Pedido #' . $row['id_pedido'] . '</h3>';
    echo '<p>Total Cuenta: $' . $row['total_cuenta'] . '</p>';
    echo '<p>Hora: ' . $row['hora'] . '</p>';
    echo '<p>Fecha: ' . $row['fecha'] . '</p>';
    echo '<p>Estado: ' . $row['estado'] . '</p>';
    echo '<p>Platillos: ' . $row['platillos'] . '</p>';
} else {
    echo '<p>No se encontraron detalles para este pedido.</p>';
}

$stmt->close();
$conn->close();
?>