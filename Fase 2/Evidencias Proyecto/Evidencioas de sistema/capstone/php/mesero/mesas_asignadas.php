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

// Obtener ID del mesero desde la solicitud
$id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;

// Consultar mesas ocupadas por el mesero a través de los pedidos activos
$sql = "SELECT DISTINCT mesa.id_mesa, mesa.cantidad_asientos, mesa.estado 
        FROM mesa 
        JOIN pedido ON mesa.id_mesa = pedido.id_mesa
        WHERE pedido.id_usuario = ? AND pedido.estado NOT IN ('Pagado')";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$mesas = [];
while ($row = $result->fetch_assoc()) {
    $mesas[] = $row;
}

$stmt->close();
$conn->close();

// Generar la lista de mesas
if (empty($mesas)) {
    echo '<li>No hay mesas que requieran de tu atención ahora. Aprovecha para descansar.</li>';
} else {
    foreach ($mesas as $mesa) {
        echo '<li class="' . ($mesa['estado'] === 'Ocupada' ? 'occupied' : '') . '">';
        echo 'Mesa #' . $mesa['id_mesa'] . ' (Asientos: ' . $mesa['cantidad_asientos'] . ')';
        echo '<div class="details expandable-content" id="details-' . $mesa['id_mesa'] . '" style="display: none;">';
        echo '<button onclick="verDetalles(' . $mesa['id_mesa'] . ')">Ver Más</button>';
        echo '<button onclick="cambiarEstado(' . $mesa['id_mesa'] . ')">Cambiar Estado</button>';
        echo '</div>';
        echo '</li>';
    }
}
?>