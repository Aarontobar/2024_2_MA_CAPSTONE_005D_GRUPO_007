<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "restaurante_bd");

// Obteniendo los datos de hoy y de ayer
$hoy = date('Y-m-d');
$ayer = date('Y-m-d', strtotime('-1 day'));

// Ventas Totales de Hoy
$result = $conn->query("SELECT SUM(total_cuenta) as total_ventas FROM Pedido WHERE fecha = '$hoy'");
$ventas_hoy = $result->fetch_assoc()['total_ventas'] ?? 0;

// Ventas Totales de Ayer
$result = $conn->query("SELECT SUM(total_cuenta) as total_ventas FROM Pedido WHERE fecha = '$ayer'");
$ventas_ayer = $result->fetch_assoc()['total_ventas'] ?? 0;

// Cantidad de Pedidos de Hoy
$result = $conn->query("SELECT COUNT(id_pedido) as pedidos_hoy FROM Pedido WHERE fecha = '$hoy'");
$pedidos_hoy = $result->fetch_assoc()['pedidos_hoy'] ?? 0;

// Cantidad de Pedidos de Ayer
$result = $conn->query("SELECT COUNT(id_pedido) as pedidos_ayer FROM Pedido WHERE fecha = '$ayer'");
$pedidos_ayer = $result->fetch_assoc()['pedidos_ayer'] ?? 0;

// Valor Promedio de los Pedidos Hoy
$valor_promedio_hoy = ($pedidos_hoy > 0) ? $ventas_hoy / $pedidos_hoy : 0;

// Cantidad de Platillos Pedidos Hoy
$result = $conn->query("SELECT SUM(cantidad) as cantidad_platillos FROM Detalle_Pedido_Platillo dp JOIN Pedido p ON dp.id_pedido = p.id_pedido WHERE p.fecha = '$hoy'");
$platillos_hoy = $result->fetch_assoc()['cantidad_platillos'] ?? 0;

// Cantidad de Platillos Pedidos Ayer
$result = $conn->query("SELECT SUM(cantidad) as cantidad_platillos FROM Detalle_Pedido_Platillo dp JOIN Pedido p ON dp.id_pedido = p.id_pedido WHERE p.fecha = '$ayer'");
$platillos_ayer = $result->fetch_assoc()['cantidad_platillos'] ?? 0;

// Calcular el porcentaje de cambio
function calcularPorcentajeCambio($valor_hoy, $valor_ayer) {
    if ($valor_ayer == 0) return ($valor_hoy > 0) ? 100 : 0;
    return (($valor_hoy - $valor_ayer) / $valor_ayer) * 100;
}

$porcentaje_ventas = calcularPorcentajeCambio($ventas_hoy, $ventas_ayer);
$porcentaje_pedidos = calcularPorcentajeCambio($pedidos_hoy, $pedidos_ayer);
$porcentaje_platillos = calcularPorcentajeCambio($platillos_hoy, $platillos_ayer);

// Obtener datos para gráficos
$result = $conn->query("SELECT fecha, SUM(total_cuenta) as total_ventas, COUNT(id_pedido) as cantidad_pedidos FROM Pedido GROUP BY fecha ORDER BY fecha DESC LIMIT 7");
$fechas = [];
$ventas = [];
$pedidos = [];

while ($fila = $result->fetch_assoc()) {
    $fechas[] = $fila['fecha'];
    $ventas[] = $fila['total_ventas'];
    $pedidos[] = $fila['cantidad_pedidos'];
}

// Convertir los datos en formato JSON
$fechas_json = json_encode(array_reverse($fechas));
$ventas_json = json_encode(array_reverse($ventas));
$pedidos_json = json_encode(array_reverse($pedidos));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card {
            margin: 15px;
        }
        .porcentaje-verde {
            color: green;
        }
        .porcentaje-rojo {
            color: red;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            padding: 15px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: flex;
            align-items: center; /* Alineación de icono y texto */
        }
        .sidebar a i {
            margin-right: 10px; /* Espacio para el icono */
        }
        .sidebar a:hover {
            background-color: #495057;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Barra lateral -->
        <?php include 'barra.php'; ?>

        <!-- Contenido principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h1 class="text-center mt-4">Panel de Administración</h1>
            <div class="row">
                <!-- Ventas Totales -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Ventas Totales Hoy</h5>
                            <h2>$<?php echo number_format($ventas_hoy, 2); ?></h2>
                            <p class="<?php echo ($porcentaje_ventas >= 0) ? 'porcentaje-verde' : 'porcentaje-rojo'; ?>">
                                <?php echo ($porcentaje_ventas >= 0) ? '+' : ''; ?>
                                <?php echo number_format($porcentaje_ventas, 2); ?>% respecto a ayer
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Cantidad de Pedidos -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Cantidad de Pedidos</h5>
                            <h2><?php echo $pedidos_hoy; ?></h2>
                            <p class="<?php echo ($porcentaje_pedidos >= 0) ? 'porcentaje-verde' : 'porcentaje-rojo'; ?>">
                                <?php echo ($porcentaje_pedidos >= 0) ? '+' : ''; ?>
                                <?php echo number_format($porcentaje_pedidos, 2); ?>% respecto a ayer
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Valor Promedio de Pedidos -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Valor Promedio de Pedidos</h5>
                            <h2>$<?php echo number_format($valor_promedio_hoy, 2); ?></h2>
                        </div>
                    </div>
                </div>
                <!-- Cantidad de Platillos -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Platillos Pedidos</h5>
                            <h2><?php echo $platillos_hoy; ?></h2>
                            <p class="<?php echo ($porcentaje_platillos >= 0) ? 'porcentaje-verde' : 'porcentaje-rojo'; ?>">
                                <?php echo ($porcentaje_platillos >= 0) ? '+' : ''; ?>
                                <?php echo number_format($porcentaje_platillos, 2); ?>% respecto a ayer
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos -->
            <div class="row">
                <div class="col-md-6">
                    <canvas id="ventasChart"></canvas>
                </div>
                <div class="col-md-6">
                    <canvas id="pedidosChart"></canvas>
                </div>
            </div>
        </main>
    </div>

    <?php include '../modulos/Chat.php'; ?>
</div>

<script>
// Datos para los gráficos
const fechas = <?php echo $fechas_json; ?>;
const ventas = <?php echo $ventas_json; ?>;
const pedidos = <?php echo $pedidos_json; ?>;

// Gráfico de Ventas Totales
const ctxVentas = document.getElementById('ventasChart').getContext('2d');
const ventasChart = new Chart(ctxVentas, {
    type: 'line',
    data: {
        labels: fechas,
        datasets: [{
            label: 'Ventas Totales ($)',
            data: ventas,
            borderColor: 'rgba(75, 192, 192, 1)',
            fill: false
        }]
    }
});

// Gráfico de Cantidad de Pedidos
const ctxPedidos = document.getElementById('pedidosChart').getContext('2d');
const pedidosChart = new Chart(ctxPedidos, {
    type: 'line',
    data: {
        labels: fechas,
        datasets: [{
            label: 'Cantidad de Pedidos',
            data: pedidos,
            borderColor: 'rgba(153, 102, 255, 1)',
            fill: false
        }]
    }
});
</script>
</body>
</html>