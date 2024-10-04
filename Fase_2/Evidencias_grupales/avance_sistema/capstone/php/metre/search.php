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

function loadReservations($conn, $name = '') {
    $name = $conn->real_escape_string($name);
    $sql = "SELECT * FROM Reserva WHERE estado_reserva = 'Pendiente'";

    if (!empty($name)) {
        $sql .= " AND (nombre_reserva LIKE '%$name%' OR apellido_reserva LIKE '%$name%')";
    }

    $sql .= " LIMIT 5";
    $result = $conn->query($sql);

    if (!$result) {
        die("Error en la consulta: " . $conn->error);
    }

    $reservations = array();
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
    return $reservations;
}

function loadAvailableTables($conn, $seats = '') {
    $seats = $conn->real_escape_string($seats);
    $sql = "SELECT * FROM Mesa WHERE estado = 'Disponible'";

    if (!empty($seats)) {
        $sql .= " AND cantidad_asientos >= $seats";
    }

    $result = $conn->query($sql);

    if (!$result) {
        die("Error en la consulta: " . $conn->error);
    }

    $tables = array();
    while ($row = $result->fetch_assoc()) {
        $tables[] = $row;
    }
    return $tables;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'searchReservations') {
    $name = isset($_GET['name']) ? $_GET['name'] : '';
    $reservations = loadReservations($conn, $name);
    if (empty($reservations)) {
        echo '<tr><td colspan="6">No hay reservaciones disponibles.</td></tr>';
    } else {
        foreach ($reservations as $reservation) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($reservation['fecha']) . '</td>';
            echo '<td>' . htmlspecialchars($reservation['hora']) . '</td>';
            echo '<td>' . htmlspecialchars($reservation['cantidad_personas']) . '</td>';
            echo '<td>' . htmlspecialchars($reservation['nombre_reserva'] . ' ' . $reservation['apellido_reserva']) . '</td>';
            echo '<td>' . htmlspecialchars($reservation['estado_reserva']) . '</td>';
            echo '<td>
                    <form method="POST" action="cambio_reservacion.php">
                        <input type="hidden" name="id_reserva" value="'. htmlspecialchars($reservation['id_reserva']) .'">
                        <button type="submit">Marcar como Realizada</button>
                    </form>
                    </td>';
            echo '</tr>';
        }
    }
} elseif ($action == 'searchTables') {
    $seats = isset($_GET['seats']) ? $_GET['seats'] : '';
    $tables = loadAvailableTables($conn, $seats);
    if (empty($tables)) {
        echo '<tr><td colspan="4">No hay mesas disponibles.</td></tr>';
    } else {
        foreach ($tables as $table) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($table['id_mesa']) . '</td>';
            echo '<td>' . htmlspecialchars($table['cantidad_asientos']) . '</td>';
            echo '<td>' . htmlspecialchars($table['estado']) . '</td>';
            echo '<td>
            <form method="POST" action="cambio_mesa.php">
                <input type="hidden" name="id_mesa" value="'. htmlspecialchars($reservation['cantidad_personas']) .'">
                <button type="submit">Marcar como Ocupada</button>
            </form>
            </td>';
            echo '</tr>';
        }
    }
}

$conn->close();
?>