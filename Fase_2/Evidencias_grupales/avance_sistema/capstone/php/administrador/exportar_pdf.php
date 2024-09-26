<?php
require('fpdf.php');
include 'conexion.php'; // Asegúrate de que este archivo tenga la conexión a la base de datos

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Título del Reporte
$pdf->Cell(0, 10, 'Reporte de Ventas y Platillos', 0, 1, 'C');
$pdf->Ln(10);

// Ventas
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Ventas', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);

// Query para obtener ventas
$fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : '';
$fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : '';

$ventasQuery = "SELECT p.id_pedido, m.id_mesa, u.nombre_usuario, p.total_cuenta, p.fecha
                FROM Pedido p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                WHERE p.fecha BETWEEN ? AND ?";

$stmt = $conn->prepare($ventasQuery);
$stmt->bind_param('ss', $fechaInicio, $fechaFin);
$stmt->execute();
$resultVentas = $stmt->get_result();
$ventasData = $resultVentas->fetch_all(MYSQLI_ASSOC);

$pdf->Cell(40, 10, 'ID Pedido', 1);
$pdf->Cell(40, 10, 'Mesa', 1);
$pdf->Cell(60, 10, 'Usuario', 1);
$pdf->Cell(40, 10, 'Total', 1);
$pdf->Cell(40, 10, 'Fecha', 1);
$pdf->Ln();

foreach ($ventasData as $venta) {
    $pdf->Cell(40, 10, $venta['id_pedido'], 1);
    $pdf->Cell(40, 10, $venta['id_mesa'], 1);
    $pdf->Cell(60, 10, $venta['nombre_usuario'], 1);
    $pdf->Cell(40, 10, $venta['total_cuenta'], 1);
    $pdf->Cell(40, 10, $venta['fecha'], 1);
    $pdf->Ln();
}

$pdf->Ln(10);

// Platillos Más Vendidos
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Platillos Más Vendidos', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);

// Query para obtener platillos
$platillosQuery = "SELECT pl.nombre_platillo, SUM(dpp.cantidad) as cantidad_vendida
                   FROM Detalle_Pedido_Platillo dpp
                   JOIN Platillos pl ON dpp.id_platillo = pl.id_platillo
                   JOIN Pedido p ON dpp.id_pedido = p.id_pedido
                   WHERE p.fecha BETWEEN ? AND ?
                   GROUP BY pl.nombre_platillo";

$stmt = $conn->prepare($platillosQuery);
$stmt->bind_param('ss', $fechaInicio, $fechaFin);
$stmt->execute();
$resultPlatillos = $stmt->get_result();
$platillosData = $resultPlatillos->fetch_all(MYSQLI_ASSOC);

$pdf->Cell(100, 10, 'Platillo', 1);
$pdf->Cell(40, 10, 'Cantidad Vendida', 1);
$pdf->Ln();

foreach ($platillosData as $platillo) {
    $pdf->Cell(100, 10, $platillo['nombre_platillo'], 1);
    $pdf->Cell(40, 10, $platillo['cantidad_vendida'], 1);
    $pdf->Ln();
}

// Output PDF
$pdf->Output();
?>