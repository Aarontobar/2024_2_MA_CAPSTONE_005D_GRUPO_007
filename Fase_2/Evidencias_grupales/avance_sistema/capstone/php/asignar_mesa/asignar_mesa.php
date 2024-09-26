<?php
// Habilitar el reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establecer la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurante_bd";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

function loadReservations($conn) {
    $sql = "SELECT * FROM Reserva WHERE estado_reserva = 'Pendiente'";
    $result = $conn->query($sql);

    if (!$result) {
        die("Error en la consulta: " . $conn->error);
    }

    $reservations = array();
    while($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
    return $reservations;
}

function loadAvailableTables($conn) {
    $sql = "SELECT * FROM Mesa WHERE estado = 'Disponible'";
    $result = $conn->query($sql);

    if (!$result) {
        die("Error en la consulta: " . $conn->error);
    }

    $tables = array();
    while($row = $result->fetch_assoc()) {
        $tables[] = $row;
    }
    return $tables;
}

$reservations = loadReservations($conn);
$tables = loadAvailableTables($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineFine</title>
    <link rel="stylesheet" href="../../css/asignar_mesa.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="content">
            <h1>Reservaciones</h1>

            <div class="search-bar">
                <form id="searchReservationForm" method="GET">
                    <input type="text" id="searchReservationName" name="name" placeholder="Buscar reservación por nombre" oninput="searchReservations()">
                </form>
            </div>

            <table id="reservationTable">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Cantidad de Personas</th>
                        <th>Nombre del Cliente</th>
                        <th>Estado de la Reservación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="reservationTableBody">
                    <?php if (empty($reservations)): ?>
                        <tr><td colspan="5">No hay reservaciones disponibles.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reservation['fecha']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['hora']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['cantidad_personas']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['nombre_reserva'] . ' ' . $reservation['apellido_reserva']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['estado_reserva']); ?></td>
                                <td>
                                    <form method="POST" action="cambio_reservacion.php">
                                        <input type="hidden" name="id_reserva" value="<?php echo htmlspecialchars($reservation['id_reserva']); ?>">
                                        <button type="submit">Marcar como Realizada</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <h1>Asignar Mesa</h1>

            <div class="search-bar asignar">
                <form id="searchMesaForm">
                    <input type="number" id="seatCount" name="seats" placeholder="Buscar mesa por número de asientos" oninput="searchTables()">

                </form>
            </div>

            <table id="mesaTable">
                <thead>
                    <tr>
                        <th>N° Mesa</th>
                        <th>Cantidad de Asientos</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="mesaTableBody">
                    <?php if (empty($tables)): ?>
                        <tr><td colspan="3">No hay mesas disponibles.</td></tr>
                    <?php else: ?>
                        <?php foreach ($tables as $table): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($table['id_mesa']); ?></td>
                                <td><?php echo htmlspecialchars($table['cantidad_asientos']); ?></td>
                                <td><?php echo htmlspecialchars($table['estado']); ?></td>
                                <td>
                                    <form method="POST" action="cambio_mesa.php">
                                        <input type="hidden" name="id_mesa" value="<?php echo htmlspecialchars($table['id_mesa']); ?>">
                                        <button type="submit">Marcar como Ocupada</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="footer">
            </div>
        </div>
    </div>
    <script src="../../js/asignar_mesa.js"></script>
</body>
</html>