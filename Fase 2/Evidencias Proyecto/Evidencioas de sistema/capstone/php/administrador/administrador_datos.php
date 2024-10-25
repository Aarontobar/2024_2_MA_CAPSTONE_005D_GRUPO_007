<?php
// Conectar a la base de datos
$host = "localhost";
$dbname = "restaurante_bd";
$username = "root";
$password = ""; // Cambia esto por tu contraseña

$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

// Obtener el total de órdenes de hoy
$total_pedidos_hoy = $conn->query("SELECT COUNT(*) FROM Pedido WHERE DATE(fecha) = CURDATE()")->fetchColumn();

// Obtener el valor total de ventas de hoy
$total_ventas_hoy = $conn->query("SELECT SUM(total_cuenta) FROM Pedido WHERE DATE(fecha) = CURDATE()")->fetchColumn();

// Obtener el valor promedio de pedidos de hoy
$promedio_ventas_hoy = $conn->query("SELECT AVG(total_cuenta) FROM Pedido WHERE DATE(fecha) = CURDATE()")->fetchColumn();

// Obtener la cantidad total de platillos vendidos hoy
$total_platillos_hoy = $conn->query("
    SELECT SUM(dp.cantidad) 
    FROM Detalle_Pedido_Platillo dp
    JOIN Pedido p ON dp.id_pedido = p.id_pedido
    WHERE DATE(p.fecha) = CURDATE()
")->fetchColumn();

// Obtener la lista de pedidos recientes
$pedidos_recientes = $conn->query("
    SELECT p.id_pedido, u.nombre AS nombre_usuario, p.total_cuenta, p.hora
    FROM Pedido p
    LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
    ORDER BY p.fecha DESC, p.hora DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Convertir datos a formato JSON para usarlos en el frontend
echo json_encode([
    "total_pedidos_hoy" => $total_pedidos_hoy,
    "total_ventas_hoy" => $total_ventas_hoy,
    "promedio_ventas_hoy" => $promedio_ventas_hoy,
    "total_platillos_hoy" => $total_platillos_hoy,
    "pedidos_recientes" => $pedidos_recientes
]);
?>