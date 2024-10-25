<?php
// Conectar a la base de datos
$servername = "localhost"; // Cambiar si es necesario
$username = "root"; // Cambiar si es necesario
$password = ""; // Cambiar si es necesario
$dbname = "restaurante_bd";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener pedidos activos del día actual
$sql = "SELECT *, tipo FROM Pedido WHERE estado IN ('recibido', 'en preparación') AND fecha = CURDATE()";
$result = $conn->query($sql);

$pedidos = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_pedido = $row['id_pedido'];
        $total_cuenta = $row['total_cuenta'];
        $hora = $row['hora'];
        $fecha = $row['fecha'];
        $estado = $row['estado'];
        $tipo = $row['tipo']; // Asegúrate de que el tipo esté en la consulta
        $prioridad = $row['prioridad']; // Asegúrate de incluir la prioridad

        // Obtener platillos del pedido
        $sqlDetalles = "SELECT p.nombre_platillo, dp.cantidad 
                        FROM Detalle_Pedido_Platillo dp 
                        JOIN Platillos p ON dp.id_platillo = p.id_platillo 
                        WHERE dp.id_pedido = $id_pedido";
        $resultDetalles = $conn->query($sqlDetalles);
        $platillos = [];
        if ($resultDetalles->num_rows > 0) {
            while ($detalle = $resultDetalles->fetch_assoc()) {
                $platillos[] = $detalle;
            }
        }

        $pedidos[] = [
            'id_pedido' => $id_pedido,
            'total_cuenta' => $total_cuenta,
            'hora' => $hora,
            'fecha' => $fecha,
            'estado' => $estado,
            'platillos' => $platillos,
            'tipo' => $tipo, // Asegúrate de incluir esto
            'prioridad' => $prioridad // Incluir prioridad
        ];
    }
}

$conn->close();

// Enviar los datos como JSON
echo json_encode($pedidos);
?>