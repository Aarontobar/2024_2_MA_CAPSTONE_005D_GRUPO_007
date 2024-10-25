<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurante_bd";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

$dateCondition = "";
if ($startDate && $endDate) {
    $dateCondition = "WHERE ped.fecha BETWEEN '$startDate' AND '$endDate'";
} elseif ($startDate) {
    $dateCondition = "WHERE ped.fecha >= '$startDate'";
} elseif ($endDate) {
    $dateCondition = "WHERE ped.fecha <= '$endDate'";
}

// Ventas por tiempo
$salesByTimeQuery = "SELECT fecha, SUM(total_cuenta) as total FROM Pedido ped $dateCondition GROUP BY fecha";
$salesByTimeResult = $conn->query($salesByTimeQuery);
$salesByTimeData = ['labels' => [], 'data' => []];
if ($salesByTimeResult && $salesByTimeResult->num_rows > 0) {
    while ($row = $salesByTimeResult->fetch_assoc()) {
        $salesByTimeData['labels'][] = $row['fecha'];
        $salesByTimeData['data'][] = $row['total'];
    }
}

// Ganancias por platillo
$earningsByDishQuery = "SELECT p.nombre_platillo, SUM(d.cantidad * p.precio) as total
                        FROM Detalle_Pedido_Platillo d
                        JOIN Platillos p ON d.id_platillo = p.id_platillo
                        JOIN Pedido ped ON d.id_pedido = ped.id_pedido
                        $dateCondition
                        GROUP BY p.nombre_platillo";
$earningsByDishResult = $conn->query($earningsByDishQuery);
$earningsByDishData = ['labels' => [], 'data' => []];
if ($earningsByDishResult && $earningsByDishResult->num_rows > 0) {
    while ($row = $earningsByDishResult->fetch_assoc()) {
        $earningsByDishData['labels'][] = $row['nombre_platillo'];
        $earningsByDishData['data'][] = $row['total'];
    }
}

// Platillo más popular
$mostPopularDishQuery = "SELECT p.nombre_platillo, SUM(d.cantidad) as count
                         FROM Detalle_Pedido_Platillo d
                         JOIN Platillos p ON d.id_platillo = p.id_platillo
                         JOIN Pedido ped ON d.id_pedido = ped.id_pedido
                         $dateCondition
                         GROUP BY p.nombre_platillo
                         ORDER BY count DESC LIMIT 1";
$mostPopularDishResult = $conn->query($mostPopularDishQuery);
$mostPopularDish = null;
if ($mostPopularDishResult && $mostPopularDishResult->num_rows > 0) {
    $mostPopularDish = $mostPopularDishResult->fetch_assoc();
}

// Tipo de pedido más usado
$orderTypeQuery = "SELECT tipo, COUNT(*) as count FROM Pedido ped $dateCondition GROUP BY tipo";
$orderTypeResult = $conn->query($orderTypeQuery);
$orderTypeData = ['labels' => [], 'data' => []];
if ($orderTypeResult && $orderTypeResult->num_rows > 0) {
    while ($row = $orderTypeResult->fetch_assoc()) {
        $orderTypeData['labels'][] = $row['tipo'];
        $orderTypeData['data'][] = $row['count'];
    }
}

// Ventas diarias
$dailySalesQuery = "SELECT fecha, SUM(total_cuenta) as total FROM Pedido ped $dateCondition GROUP BY fecha";
$dailySalesResult = $conn->query($dailySalesQuery);
$dailySalesData = ['labels' => [], 'data' => []];
if ($dailySalesResult && $dailySalesResult->num_rows > 0) {
    while ($row = $dailySalesResult->fetch_assoc()) {
        $dailySalesData['labels'][] = $row['fecha'];
        $dailySalesData['data'][] = $row['total'];
    }
}

// Ingresos mensuales
$monthlyRevenueQuery = "SELECT MONTH(fecha) as mes, SUM(total_cuenta) as total FROM Pedido ped $dateCondition GROUP BY mes";
$monthlyRevenueResult = $conn->query($monthlyRevenueQuery);
$monthlyRevenueData = ['labels' => [], 'data' => []];
if ($monthlyRevenueResult && $monthlyRevenueResult->num_rows > 0) {
    while ($row = $monthlyRevenueResult->fetch_assoc()) {
        $monthlyRevenueData['labels'][] = $row['mes'];
        $monthlyRevenueData['data'][] = $row['total'];
    }
}

$response = [
    'sales_by_time' => $salesByTimeData,
    'earnings_by_dish' => $earningsByDishData,
    'most_popular_dish' => $mostPopularDish,
    'order_type' => $orderTypeData,
    'daily_sales' => $dailySalesData,
    'monthly_revenue' => $monthlyRevenueData
];

echo json_encode($response);

$conn->close();
?>