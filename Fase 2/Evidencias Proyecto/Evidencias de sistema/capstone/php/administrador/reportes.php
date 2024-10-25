<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Reporte del Restaurante</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/reportes.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <style>
        body {
            margin: 0;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            padding: 15px;
            position: fixed;
            width: 17rem; /* Ajustar según el ancho de la barra de navegación */
            top: 0;
            left: 0;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: flex;
            align-items: center;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 17rem; /* Ajustar según el ancho de la barra de navegación */
            padding: 20px;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .container {
            margin-top: 20px;
        }
        .graph-container {
            margin-bottom: 40px;
        }
        .form-control, .btn {
            margin-top: 10px;
        }
        .table-container {
            margin-top: 20px;
        }
        .hidden { display: none; } /* Clase para ocultar elementos en PDF */
    </style>
</head>
<body>
    <?php include 'barra.php'; ?>

    <div class="content">
        <h1 class="mb-4">Reporte del Restaurante</h1>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-3">
                <label for="startDate" class="form-label">Fecha de Inicio</label>
                <input type="date" class="form-control" id="startDate">
            </div>
            <div class="col-md-3">
                <label for="endDate" class="form-label">Fecha de Fin</label>
                <input type="date" class="form-control" id="endDate">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary" id="applyFilters">Aplicar Filtros</button>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-success me-2" id="exportExcel">Exportar a Excel</button>
                <button class="btn btn-danger" id="exportPdf">Exportar a PDF</button>
            </div>
        </div>

        <!-- Información General -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h4>Informe General del Mes</h4>
                <p id="monthlySummary"></p>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row">
            <div class="col-md-6 graph-container">
                <h4>Ventas por Rango de Tiempo</h4>
                <canvas id="salesByTimeChart"></canvas>
            </div>

            <div class="col-md-6 graph-container">
                <h4>Ganancias por Platillo</h4>
                <canvas id="earningsByDishChart"></canvas>
            </div>

            <div class="col-md-6 graph-container">
                <h4>Platillo Más Vendido</h4>
                <p id="mostPopularDishText"></p>
            </div>

            <div class="col-md-6 graph-container">
                <h4>Tipo de Pedido Más Usado</h4>
                <canvas id="orderTypeChart"></canvas>
            </div>

            <div class="col-md-6 graph-container">
                <h4>Ventas Diarias</h4>
                <canvas id="dailySalesChart"></canvas>
            </div>

            <div class="col-md-6 graph-container">
                <h4>Ingresos Mensuales</h4>
                <canvas id="monthlyRevenueChart"></canvas>
            </div>
        </div>

        <!-- Tablas -->
        <div class="table-container">
            <h4>Órdenes y Reservas</h4>
            <div id="orders-table" class="table-responsive mb-4">
                <!-- Aquí se llenará con una tabla de órdenes -->
            </div>
            <div id="reservations-table" class="table-responsive">
                <!-- Aquí se llenará con una tabla de reservas -->
            </div>
        </div>
    </div>

    <script src="../../js/reportes.js"></script>
</body>
</html>